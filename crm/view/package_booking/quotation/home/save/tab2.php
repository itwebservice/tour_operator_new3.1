<form id="frm_tab2">

    <div class="app_panel" style="padding-top: 30px;">

        <div class="container" style="width:100% !important;">
            <div class="row">

                <div class="col-md-3 col-sm-4 col-xs-12 mg_bt_20" id="package_div">
                    <?php
					$sq_tours = mysqlQuery("select * from custom_package_master where status !='Inactive'"); ?>
                    <select name="dest_name" id="dest_name" title="Select Destination"
                        onchange="load_packages_with_filter()" style="width:100%">
                        <option value="">*Select Destination</option>
                        <?php
						$sq_query = mysqlQuery("select * from destination_master where status != 'Inactive'");
						while ($row_dest = mysqli_fetch_assoc($sq_query)) { ?>
                        <option value="<?php echo $row_dest['dest_id']; ?>"><?php echo $row_dest['dest_name']; ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-4 col-xs-12 mg_bt_20">
                    <select name="nights_filter" id="nights_filter" title="Filter by Nights" 
                        onchange="filter_packages_by_nights()" style="width:100%">
                        <option value="">All Nights</option>
                        <?php
                        // Generate options for 1 to 30 nights
                        for($i = 1; $i <= 30; $i++) {
                            echo "<option value='$i'>$i Night" . ($i > 1 ? 's' : '') . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6 col-sm-4 col-xs-12 mg_bt_20 text-right">
                    <a href="../../../../custom_packages/master/index.php" target='_blank' class="btn btn-info btn-sm"><i class="fa fa-plus"></i>&nbsp;&nbsp;Package Tour</a>
                    <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Note : The Package is not available for this Destination.Please create here."><i class="fa fa-question-circle"></i></button>
                </div>
                <div class="col-md-12 col-sm-8 col-xs-12 no-pad" id="package_name_div">
                </div>
            </div>

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


// Initialize nights filter when tab2 loads
$(document).ready(function() {
    var selected_nights = sessionStorage.getItem('selected_nights');
    
    if (selected_nights) {
        $('#nights_filter').val(selected_nights);
        // Trigger the filter when page loads
        load_packages_with_filter();
    }
});

// Function to load packages with both destination and nights filter
function load_packages_with_filter() {
    var dest_id = $('#dest_name').val();
    var total_nights = $('#nights_filter').val() || sessionStorage.getItem('selected_nights');
    
    console.log("load_packages_with_filter called - dest_id:", dest_id, "total_nights:", total_nights);
    
    if (dest_id) {
        // Update sessionStorage with current nights selection
        if (total_nights) {
            sessionStorage.setItem('selected_nights', total_nights);
        }
        
        console.log("Calling package_dynamic_reflect_with_nights with dest_id:", dest_id, "nights:", total_nights);
        // Call the package loading function with nights parameter
        package_dynamic_reflect_with_nights('dest_name', total_nights);
    } else {
        console.log("No destination selected, cannot load packages");
    }
}

// Function to filter packages by nights in tab2
function filter_packages_by_nights() {
    var total_nights = $('#nights_filter').val();
    
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
        url: base_url + 'view/package_booking/quotation/inc/get_packages.php',
        data: ajax_data,
        success: function (result) {
            $('#package_name_div').html(result);
        },
        error: function (result) {
            console.log('Package loading error:', result.responseText);
        }
    });
}

function switch_to_tab1() {
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
            package_p_id_arr: package_p_id_arr,
            package_id_arr: package_id_arr
        });

        // Store in sessionStorage for later use
        sessionStorage.setItem('itinerary_data', JSON.stringify({
            attraction_arr: attraction_arr,
            program_arr: program_arr,
            stay_arr: stay_arr,
            meal_plan_arr: meal_plan_arr,
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

                //Hotel Info
                var table = document.getElementById("tbl_package_tour_quotation_dynamic_hotel");
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
                from_date: from_date,total_adult:total_adult
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
        $('#tab_daywise_head').addClass('active');
        $('.bk_tab').removeClass('active');
        $('#tab_daywise').addClass('active');
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
    
    // Get stored nights
    var storedNights = sessionStorage.getItem('selected_nights');
    
    console.log("Stored destination ID:", storedDestId);
    console.log("Stored destination name:", storedDestName);
    console.log("Stored nights:", storedNights);
    
    // Set destination if available
    if (storedDestId && storedDestName) {
        $('#dest_name').val(storedDestId);
        console.log("Set destination to:", storedDestName);
    }
    
    // Set nights if available
    if (storedNights) {
        $('#nights_filter').val(storedNights);
        console.log("Set nights filter to:", storedNights);
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

</script>