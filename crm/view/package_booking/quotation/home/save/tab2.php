<form id="frm_tab2">

    <div class="app_panel" style="padding-top: 30px;">

        <div class="container" style="width:100% !important;">
            <div class="row">

                <div class="col-md-3 col-sm-4 col-xs-12 mg_bt_20" id="package_div">
                    <?php
                    $sq_tours = mysqlQuery("select * from custom_package_master where status !='Inactive'"); ?>
                    <select name="dest_name" id="dest_name" title="Select Destination"
                        onchange="load_packages_with_filter()" style="width:100%" data-add-new-option="true">
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
            <div class="ai-chat-container">
                <button class="btn btn-info btn-sm ico_left" id="aiToggleBtn" type="button" aria-label="Toggle AI assistant">
                    <span class="ai-toggle-icon"><i class=""><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M11.6838 0L10.8879 2.38783L8.50006 3.18377V4.13245L10.8879 4.9284L11.6838 7.31623H12.6325L13.4285 4.9284L15.8163 4.13245V3.18377L13.4285 2.38783L12.6325 0H11.6838Z" fill="white"/>
<path d="M5.01289 8.51283L6.18383 5H7.13251L8.30346 8.51283L11.8163 9.68377V10.6325L8.30346 11.8034L7.13251 15.3162H6.18383L5.01289 11.8034L1.50006 10.6325V9.68377L5.01289 8.51283Z" fill="white"/>
<path d="M2.17582 1L1.63186 2.63186L0 3.17582V3.82416L1.63186 4.36811L2.17582 5.99997H2.82416L3.36811 4.36811L4.99997 3.82416V3.17582L3.36811 2.63186L2.82416 1H2.17582Z" fill="white"/>
</svg>
</i></span>
                    <span>AI</span>
                </button>
                <div class="ai-chat-box" id="aiChatBox" aria-hidden="true">
                    <textarea id="aiMessageInput" placeholder="Type your message..."></textarea>
                    <button type="button" class="send-btn" id="btnAnalyseMessage">
                        <svg viewBox="0 0 24 24">
                        <path d="M2 21L23 12L2 3V10L17 12L2 14V21Z"/>
                        </svg>
                    </button>
                </div>
                <div id="aiApiInfo"></div>
                
                <div id="aiItineraryLegend" style="display: none; margin: 0px 75px;">
                    <legend>Tour Itinerary</legend>
                </div>
                
                <div class="ai-itinerary-row" id="aiItineraryRow" aria-hidden="true">
                    <label class="ai-check-wrap" for="chk-program">
                        <input id="chk-program" type="checkbox">
                    </label>
                    <input type="text" class="ai-field ai-special" placeholder="*Special Attraction" value="">
                    <div style="position: relative; display: flex; flex-direction: column; flex: 1;">
                        <span class="style_text" style="position: absolute; right: 12px; top: -18px; display: flex; gap: 15px; z-index: 10; background: transparent; padding: 0;">
                            <span class="style_text_b" data-wrapper="**" style="font-weight: 700; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span>
                        </span>
                        <textarea class="ai-field ai-day-program" placeholder="*Day-wise Program"></textarea>
                    </div>
                    <input type="text" class="ai-field ai-stay" placeholder="*Overnight Stay" value="">
                    <select class="ai-field ai-meal">
                        <option selected>Meal Plan</option>
                        <option>Breakfast</option>
                        <option>Lunch</option>
                        <option>Dinner</option>
                        <option>B+L</option>
                        <option>B+D</option>
                        <option>L+D</option>
                        <option>B+L+D</option>
                        <option>Room Only</option>
                        <option>No Meals</option>
                        <option>All Inclusive</option>
                    </select>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <button type="button" class="ai-plus-btn" aria-label="Add itinerary" style="padding: 10px;">+</button>
                        <div style="position: relative; flex: 1;">
                            <label class="btn btn-sm btn-success" for="ai-day-image" style="margin-bottom: 5px; font-size: 12px; border-radius: 4px; display: inline-block;">
                                <i class="fa fa-image"></i> Upload Image
                            </label>
                            <input type="file" accept="image/*" id="ai-day-image" name="ai-day-image" onchange="handleAiImageUpload(event, 'ai-image-preview')" style="display:none;"/>
                            <div id="ai-image-preview" style="margin-top: 5px; display: none;">
                                <div class="image-zoom-container" style="height: 100px; width: 100px; overflow: hidden; border: 2px solid #ddd; border-radius: 8px; position: relative;">
                                    <img id="ai-preview-img" src="" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 6px;">
                                    <button type="button" onclick="removeAiImage('ai-day-image', 'ai-image-preview')" title="Remove Image" style="position: absolute; top: 5px; right: 5px; background-color: #dc3545; color: #fff; border: none; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;" id="ai-remove-btn">×</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin: 20px 60px 0px 60px;">
                    <div class="col-md-6" id="ai-inclusions-info">
                        <h4>Inclusions</h4>
                        <hr />
                        <textarea id="aiInclusionsInformation" name="exclusions" placeholder="Exclusions" title="Exclusions" rows="4"></textarea>
                        <!-- <div id="aiInclusionsInformation"></div> -->
                    </div>
                    <div class="col-md-6" id="ai-exclusions-info">
                        <h4>Exclusions</h4>
                        <hr />
                        <textarea id="aiExclusionsInformation" name="exclusions" placeholder="Exclusions" title="Exclusions" rows="4"></textarea>
                        <!-- <div id="aiExclusionsInformation"></div> -->
                    </div>
                </div>
                <script>
                    const aiToggleBtn = document.getElementById("aiToggleBtn");
                    const aiChatBox = document.getElementById("aiChatBox");
                    const aiItineraryRow = document.getElementById("aiItineraryRow");
                    const aiItineraryLegend = document.getElementById("aiItineraryLegend");

                    aiToggleBtn.addEventListener("click", function () {
                        aiChatBox.classList.toggle("show");
                        const isVisible = aiChatBox.classList.contains("show");
                        aiChatBox.setAttribute("aria-hidden", String(!isVisible));
                        aiItineraryRow.classList.toggle("show", isVisible);
                        aiItineraryRow.setAttribute("aria-hidden", String(!isVisible));
                        aiItineraryLegend.style.display = isVisible ? "block" : "none";
                    });

                    function selectMealPlan($mealSelect, mealValue) {
                        if (!mealValue) {
                            $mealSelect.val('Meal Plan');
                            return;
                        }
                        var target = String(mealValue).trim().toLowerCase();
                        if (target === 'n/a' || target === 'na' || target === 'no meal' || target === 'no meals') {
                            $mealSelect.val('No Meals');
                            return;
                        }
                        var matchedValue = null;

                        $mealSelect.find('option').each(function () {
                            var optionText = String($(this).text() || '').trim().toLowerCase();
                            if (optionText === target) {
                                matchedValue = $(this).val();
                                return false;
                            }
                        });

                        if (matchedValue !== null) {
                            $mealSelect.val(matchedValue);
                        } else {
                            $mealSelect.val('Meal Plan');
                        }
                    }

                    function renderDetailedProgramRows(parsedData) {
                        var programs = parsedData && parsedData.itinerary && parsedData.itinerary.detailed_program
                            ? parsedData.itinerary.detailed_program
                            : [];

                        if (!Array.isArray(programs) || !programs.length) {
                            return false;
                        }

                        $('.ai-itinerary-row-clone').remove();
                        var $template = $('#aiItineraryRow');

                        programs.forEach(function (item, index) {
                            var $row = index === 0 ? $template : $template.clone(true, true);
                            if (index > 0) {
                                var uniqueSuffix = '_clone_' + index;
                                $row.removeAttr('id');
                                $row.addClass('ai-itinerary-row-clone');
                                
                                var $fileInput = $row.find('input[type="file"]');
                                var oldFileId = $fileInput.attr('id');
                                var newFileId = oldFileId ? oldFileId + uniqueSuffix : 'ai-day-image' + uniqueSuffix;
                                $fileInput.attr('id', newFileId);

                                var $label = $row.find('label[for="' + oldFileId + '"]');
                                if ($label.length === 0 && oldFileId) {
                                    $label = $row.find('label[for="ai-day-image"]');
                                }
                                $label.attr('for', newFileId);
                                
                                var $preview = $row.find('#ai-image-preview');
                                if ($preview.length > 0) {
                                    var newPreviewId = 'ai-image-preview' + uniqueSuffix;
                                    $preview.attr('id', newPreviewId);
                                    $preview.find('#ai-preview-img').attr('id', 'ai-preview-img' + uniqueSuffix);
                                    $preview.find('#ai-remove-btn').attr('id', 'ai-remove-btn' + uniqueSuffix)
                                        .attr('onclick', 'removeAiImage("' + newFileId + '", "' + newPreviewId + '")');
                                }
                                
                                $fileInput.attr('onchange', 'handleAiImageUpload(event, "' + newPreviewId + '")');
                                $row.insertAfter($('.ai-itinerary-row').last());
                            }

                            $row.attr('aria-hidden', 'false').addClass('show');
                            $row.find('input[type="checkbox"]').prop('checked', true);
                            $row.find('.ai-special').val(item && item.special_attraction ? item.special_attraction : '');
                            $row.find('.ai-day-program').val(item && item.day_wise_program ? item.day_wise_program : '');
                            $row.find('.ai-stay').val(item && item.overnight_stay ? item.overnight_stay : '');
                            selectMealPlan($row.find('.ai-meal'), item ? item.meal_plan : null);
                        });

                        return true;
                    }

                    function renderInfoList(selector, items) {
                        var $target = $(selector);
                        var list = [];

                        if (Array.isArray(items))
                            list = items;
                        else if (typeof items === 'string' && items.trim() !== '')
                            list = [items.trim()];
                        else if (items && typeof items === 'object') {
                            list = Object.values(items).filter(function(v) {
                                return v !== null && v !== undefined && String(v).trim() !== '';
                            });
                        }

                        if (!list.length) {
                            if ($target.is('textarea')) {
                                $target.val('');

                                if ($target.data('wysiwyg'))
                                    $target.wysiwyg('setContent', '');
                                
                            } else
                                $target.html('');
                            
                            return false;
                        }

                        var html = '<ul>';
                        list.forEach(function (item) {
                            html += '<li>' + $('<div/>').text(item || '').html() + '</li>';
                        });
                        html += '</ul>';

                        if ($target.is('textarea')) {
                            if ($target.data('wysiwyg'))
                                $target.wysiwyg('setContent', html);
                            else
                                $target.val(list.join('\n'));
                            
                            $target.trigger('change');
                        } else
                            $target.html(html);
                        
                        return true;
                    }

                    function toggleInfoSection(sectionSelector, hasContent) {
                        if (hasContent) {
                            $(sectionSelector).show();
                        } else {
                            $(sectionSelector).hide();
                        }
                    }

                    toggleInfoSection('#ai-inclusions-info', false);
                    toggleInfoSection('#ai-exclusions-info', false);

                    $('#btnAnalyseMessage').on('click', function () {
                        var message = ($('#aiMessageInput').val() || '').trim();
                        var base_url = $('#base_url').val();

                        if (!message) {
                            $('#aiApiInfo').text('Please enter a message.');
                            return;
                        }

                        $('#aiApiInfo').text('');
                        $('#aiApiInfo').text('Please Wait Analysing...');
                        renderInfoList('#aiInclusionsInformation', []);
                        renderInfoList('#aiExclusionsInformation', []);
                        toggleInfoSection('#ai-inclusions-info', false);
                        toggleInfoSection('#ai-exclusions-info', false);
                        $('#btnAnalyseMessage').prop('disabled', true);

                        $.ajax({
                            type: 'post',
                            url: base_url + 'controller/gemini/gemini.php',
                            dataType: 'json',
                            data: { text: message },
                            success: function (response) {
                                if (response && response.error) {
                                    $('#aiApiInfo').text(response.error);
                                    return;
                                }

                                if (response && response.status) {
                                    //$('#aiApiInfo').text(response.reply || '');
                                    $('#aiApiInfo').text('');

                                    var parsed = null;
                                    try {
                                        parsed = JSON.parse(response.reply || '{}');
                                    } catch (e) {
                                        parsed = null;
                                    }

                                    if (!parsed && response.raw)
                                        parsed = response.raw;

                                    var hasInclusions = renderInfoList(
                                        '#aiInclusionsInformation',
                                        parsed && parsed.itinerary ? parsed.itinerary.inclusions : []
                                    );
                                    var hasExclusions = renderInfoList(
                                        '#aiExclusionsInformation',
                                        parsed && parsed.itinerary ? parsed.itinerary.exclusions : []
                                    );
                                    toggleInfoSection('#ai-inclusions-info', hasInclusions);
                                    toggleInfoSection('#ai-exclusions-info', hasExclusions);

                                    if (!renderDetailedProgramRows(parsed)) {
                                        $('#aiApiInfo').text(response.reply || 'No detailed program found.');
                                    }
                                } else {
                                    var errorMsg = (response && (response.error || response.Error))
                                        ? (response.error || response.Error)
                                        : 'Failed to analyse message.';
                                    $('#aiApiInfo').text(errorMsg);
                                }
                            },
                            error: function (xhr) {
                                var errorMsg = 'Request failed.';
                                
                                if (xhr && xhr.responseJSON && xhr.responseJSON.error)
                                    errorMsg = xhr.responseJSON.error;
                                else if (xhr && xhr.responseText)
                                    errorMsg = xhr.responseText;

                                $('#aiApiInfo').text(errorMsg);
                            },
                            complete: function () {
                                $('#btnAnalyseMessage').prop('disabled', false);
                            }
                        });
                    });

                    $('#aiMessageInput').on('input', function () {
                        $('#aiApiInfo').text('');
                    });

                    $(document).ready(function () {
                        var $featureEditors = $('#aiInclusionsInformation, #aiExclusionsInformation');
                        $featureEditors.each(function () {
                            var id = this.id;
                            if (window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances[id])
                                CKEDITOR.instances[id].destroy(true);
                        });
                        $featureEditors.each(function () {
                            var $el = $(this);
                            if ($el.data('wysiwyg'))
                                $el.wysiwyg('destroy');
                        });

                        $featureEditors.wysiwyg({
                            controls: 'bold,italic,|,undo,redo,image|h1,h2,h3,decreaseFontSize,highlight',
                            initialContent: ''
                        });
                    });
                </script>
            </div>
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

</form>
<?= end_panel() ?>

<script>
$('#dest_name').select2();
if (typeof initAllDestinationSelectAddNew === 'function') {
    initAllDestinationSelectAddNew('#frm_tab2');
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
        var tab1Destination = $('#tour_name').val();
        console.log("Tab2 clicked - Tab1 destination:", tab1Destination);
        
        if (tab1Destination) {
            var destinations = JSON.parse($('#destinations').val() || '[]');
            for (var i = 0; i < destinations.length; i++) {
                if (destinations[i].label === tab1Destination) {
                    console.log("Tab2 clicked - Found matching destination:", destinations[i]);
                    $('#dest_name').val(destinations[i].dest_id).trigger('change');
                    sessionStorage.setItem('selected_destination_id', destinations[i].dest_id);
                    sessionStorage.setItem('selected_destination_name', destinations[i].label);
                    console.log("Tab2 clicked - Destination set to:", destinations[i].label);
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

    // Function to load packages with both destination and nights filter
    function load_packages_with_filter() {
        var dest_id = $('#dest_name').val();
        var total_nights = $('#nights_filter').val() || sessionStorage.getItem('selected_nights');

        console.log("load_packages_with_filter called - dest_id:", dest_id, "total_nights:", total_nights);

        // Prevent multiple simultaneous loads
        if (isLoadingPackages) {
            console.log("Package loading already in progress, skipping...");
            return;
        }

        // Prevent loading same data
        if (lastLoadedDestId === dest_id && lastLoadedNights === total_nights) {
            console.log("Same data already loaded, skipping...");
            return;
        }

        // Clear any existing timer
        if (packageLoadingTimer) {
            clearTimeout(packageLoadingTimer);
        }

        if (dest_id) {
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
    function saveItineraryData() {
        var attraction_arr = [];
        var program_arr = [];
        var stay_arr = [];
        var meal_plan_arr = [];
        var day_image_arr = [];
        var package_p_id_arr = [];
        var package_id_arr = [];

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

                            // Check if new image was uploaded
                            var rowOffset = i + 1;
                            if (window.quotationImages && window.quotationImages[rowOffset]) {
                                var imageData = window.quotationImages[rowOffset];
                                if (imageData.image_url) {
                                    img = imageData.image_url;
                                }
                            }

                            day_image_arr.push(img);
                            console.log("Added image for row", i, ":", img);

                            // Store image data if an image is selected (using the global storage)
                            var dayOffset = i + 1;
                            if (window.quotationImages && window.quotationImages[dayOffset]) {
                                var imageData = {
                                    package_id: package_p_id,
                                    day_number: dayOffset,
                                    file: window.quotationImages[dayOffset].file,
                                    offset: dayOffset
                                };

                                // Store image for later upload
                                if (!window.itineraryImages) {
                                    window.itineraryImages = [];
                                }
                                window.itineraryImages.push(imageData);
                                console.log("Collected image for day " + dayOffset + ", package " + package_p_id + ", file: " + imageData.file.name);
                            } else {
                                console.log("No image found for day " + dayOffset);
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
            if (package_id_arr.length == 0) {
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


            $.ajax({

                type: 'post',

                url: '../save/package_hotel_info.php',

                data: {
                    package_id_arr: package_id_arr,
                    from_date: from_date
                },

                success: function(result) {

                    //Hotel Info - Preserve existing manually added packages
                    var table = document.getElementById("tbl_package_tour_quotation_dynamic_hotel");
                    
                    // Check if there are manually added packages (more than just the header)
                    var hasExistingPackages = table.rows.length > 1;
                    var existingPackageCount = table.rows.length - 1; // Subtract header row
                    
                    console.log('Tab2 Next: Existing packages count:', existingPackageCount);
                    
                    // Only clear and rebuild if there are no existing packages
                    if (!hasExistingPackages) {
                        console.log('Tab2 Next: No existing packages, clearing and rebuilding table');
                        
                        if (table.rows.length == 1) {
                            for (var k = 1; k < table.rows.length; k++) {
                                document.getElementById("tbl_package_tour_quotation_dynamic_hotel")
                                    .deleteRow(k);
                            }
                        } else {
                            while (table.rows.length > 1) {
                                document.getElementById("tbl_package_tour_quotation_dynamic_hotel")
                                    .deleteRow(k);
                                table.rows.length--;
                            }
                        }

                        var hotel_arr = JSON.parse(result);
                        if (table.rows.length != hotel_arr.length) {
                            for (var j = 0; j < hotel_arr.length - 1; j++) {
                                addRow('tbl_package_tour_quotation_dynamic_hotel');
                            }
                        }
                    } else {
                        console.log('Tab2 Next: Preserving existing packages, skipping table rebuild');
                        // Don't clear the table, just continue with existing data
                        var hotel_arr = JSON.parse(result);
                    }

                    // Only populate hotel data if we rebuilt the table (no existing packages)
                    if (!hasExistingPackages) {
                        console.log('Tab2 Next: Populating hotel data for rebuilt table');
                        for (var i = 0; i < hotel_arr.length; i++) {
                            var row = table.rows[i];
                            row.cells[1].childNodes[0].value = (i + 1);
                            city_lzloading(row.cells[3].childNodes[0]);
                            var newOption = $("<option selected='selected'></option>").val(hotel_arr[i]
                                ['city_id']).text(hotel_arr[i]['city_name']);
                            $(row.cells[3].childNodes[0]).append(newOption).trigger('change.select2');
                            $(row.cells[4].childNodes[0]).html('<option value="' + hotel_arr[i][
                                'hotel_id1'
                            ] + '">' + hotel_arr[i]['hotel_name'] + '</option>');
                            row.cells[6].childNodes[0].value = hotel_arr[i]['check_in_date'];
                            row.cells[7].childNodes[0].value = hotel_arr[i]['check_out_date'];
                            row.cells[8].childNodes[0].value = hotel_arr[i]['hotel_type'];
                            row.cells[9].childNodes[0].value = total_days;
                            row.cells[10].childNodes[0].value = '';
                            row.cells[12].childNodes[0].value = hotel_arr[i]['package_name'];
                            row.cells[14].childNodes[0].value = hotel_arr[i]['package_id'];

                            $('#' + row.cells[4].childNodes[0].id).select2().trigger("change");
                            document.getElementById(row.cells[2].childNodes[0].id).selectedIndex = 0;
                            $('#' + row.cells[2].childNodes[0].id).select2().trigger("change");
                            document.getElementById(row.cells[5].childNodes[0].id).selectedIndex = 0;
                            $('#' + row.cells[5].childNodes[0].id).select2().trigger("change");
                            calculate_total_nights(row.cells[7].childNodes[0].id);
                        }
                    } else {
                        console.log('Tab2 Next: Skipping hotel data population - preserving existing packages');
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
                    if (table.rows.length == 1) {
                        for (var k = 1; k < table.rows.length; k++) {
                            document.getElementById("tbl_package_tour_quotation_dynamic_transport")
                                .deleteRow(k);
                        }
                    } else {
                        while (table.rows.length > 1) {
                            document.getElementById("tbl_package_tour_quotation_dynamic_transport")
                                .deleteRow(k);
                            table.rows.length--;
                        }
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

                        row.cells[3].childNodes[0].value = $from_date;
                        row.cells[4].childNodes[0].value = $to_date;
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

            get_hotel_cost();
            get_excursion_amount();
            get_transport_cost();

            // Store itinerary data in sessionStorage for later saving
            saveItineraryData();

            $('#tab2_head').addClass('done');
            $('#tab3_head').addClass('active');
            $('.bk_tab').removeClass('active');
            $('#tab3').addClass('active');
            $('html, body').animate({
                scrollTop: $('.bk_tab_head').offset().top
            }, 200);
        }

    });

        // Load stored values from Tab 1 when Tab 2 loads
    $(document).ready(function() {
        console.log("Tab 2 loading - checking for stored destination and nights...");
        
        // Get stored destination
        var storedDestId = sessionStorage.getItem('selected_destination_id');
        var storedDestName = sessionStorage.getItem('selected_destination_name');
        
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
        var tab1Destination = $('#tour_name').val();
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
                    load_packages_with_filter();
                } else {
                    console.error("load_packages_with_filter function not found");
                }
            }, 1000); // Increased delay to ensure everything is loaded
        } else {
            console.log("No stored destination found, not loading packages automatically");
        }
    });

    // Event delegation for B and U text formatting buttons
    $(document).on('click', '.style_text_b, .style_text_u', function() {
        var wrapper = $(this).data('wrapper');

        // Match existing get_packages.php behavior for selecting the target textarea.
        var textarea = $(this).parents('.style_text').siblings('.day_program')[0];
        if (!textarea) {
            textarea = $(this).parents('.style_text').siblings('.ai-day-program')[0];
        }
        if (!textarea) {
            console.error('Textarea not found for formatting');
            return;
        }

        var start = textarea.selectionStart;
        var end = textarea.selectionEnd;
        var selectedText = textarea.value.substring(start, end);

        // Wrap selected text with markdown wrappers (** / __)
        var wrappedText = wrapper + selectedText + wrapper;
        textarea.value = textarea.value.substring(0, start) + wrappedText + textarea.value.substring(end);

        // Keep selection behavior aligned to existing implementation
        textarea.selectionStart = start;
        textarea.selectionEnd = end + wrapper.length * 2;

        // Convert markdown wrappers to HTML tags (same as get_packages.php)
        var text = textarea.value;
        var content = text.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');
        content = content.replace(/__(.*?)__/g, '<u>$1</u>');
        textarea.value = content;
    });

    // Handle AI day image upload with optional preview ID parameter
    function handleAiImageUpload(event, previewId) {
        const file = event.target.files[0];
        if (!file) return;

        // Use specific preview ID if provided, otherwise default to ai-image-preview
        previewId = previewId || 'ai-image-preview';
        const fileInputId = event.target.id;

        const reader = new FileReader();
        reader.onload = function(e) {
            const imgSrc = e.target.result;
            const $preview = $('#' + previewId);
            const $img = $preview.find('img');
            const $removeBtn = $preview.find('button');
            const $fileInput = $('#' + fileInputId);
            const $uploadLabel = $fileInput.parent().find('label');

            // Hide the upload label and show preview
            $uploadLabel.hide();
            $preview.show();
            $img.attr('src', imgSrc).show();
            $removeBtn.show();

            // Store image data for form submission
            if (!window.aiImages) {
                window.aiImages = {};
            }
            window.aiImages[fileInputId] = {
                file: file,
                data: imgSrc
            };

            console.log('AI image uploaded for input ID ' + fileInputId + ':', file.name);
        };

        reader.readAsDataURL(file);
    }

    // Remove AI day image with optional IDs
    function removeAiImage(fileInputId, previewId) {
        // Use provided IDs or defaults
        fileInputId = fileInputId || 'ai-day-image';
        previewId = previewId || 'ai-image-preview';

        const $fileInput = $('#' + fileInputId);
        const $preview = $('#' + previewId);
        const $img = $preview.find('img');
        const $removeBtn = $preview.find('button');
        const $uploadLabel = $fileInput.parent().find('label');

        $preview.hide();
        $img.attr('src', '').hide();
        $removeBtn.hide();
        $fileInput.val('');
        $uploadLabel.show();

        if (window.aiImages) {
            delete window.aiImages[fileInputId];
        }

        console.log('AI image removed for input ID: ' + fileInputId);
    }
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
</script>
