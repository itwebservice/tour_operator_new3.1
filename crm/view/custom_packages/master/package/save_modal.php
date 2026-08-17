<?php
$vehcile_id_str = "vehicle_name1";
?>
<form id="frm_package_master_save">

    <div class="app_panel">
        <!--=======Header panel======-->
        <div class="container-fluid mg_tp_10">
            <div class="app_panel_content no-pad">
                <div class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_20">
                    <legend>Tour Information</legend>
                    <div class="row mg_bt_10">
                        <div class="col-xs-12 no-pad mg_bt_20 mg_tp_20">
                            <div class="col-md-3 col-sm-3">
                                <select id="dest_name_s" name="dest_name_s" title="Select Destination" class="form-control" style="width:100%" onchange="get_dest_image(this.id)" data-add-new-option="true">
                                    <option value="">*Destination</option>
                                    <?php
                                    $sq_query = mysqlQuery("select * from destination_master where status != 'Inactive'");
                                    while ($row_dest = mysqli_fetch_assoc($sq_query)) { ?>
                                        <option value="<?php echo $row_dest['dest_id']; ?>">
                                            <?php echo $row_dest['dest_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                <select name="tour_type" id="tour_type" title="Tour Type" onchange="incl_reflect(this.id,'')" required>
                                    <option value="">*Tour Type</option>
                                    <option value="Domestic">Domestic</option>
                                    <option value="International">International</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                <select name="currency_code" id="currency_code" title="Currency" style="width:100%" data-toggle="tooltip" required>
                                    <option value=''>*Select Currency</option>
                                    <?php
                                    $sq_currency = mysqlQuery("select * from currency_name_master order by currency_code");
                                    while ($row_currency = mysqli_fetch_assoc($sq_currency)) {
                                    ?>
                                        <option value="<?= $row_currency['id'] ?>"><?= $row_currency['currency_code'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-3 mg_bt_10_xs">
                                <select id="dest_image" name="dest_image" title="Destination Image" class="form-control">
                                    <option value="">Select Image</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 no-pad mg_bt_20">
                            <div class="col-md-3 col-sm-3 mg_bt_10_xs">
                                <input type="text" id="package_name" name="package_name" onchange="package_name_validation(this.id);package_name_check(this.id);"  oninput="updateSlug();" class="form-control" placeholder="*Package Name" title="Package Name" />
                                <button type="button" class="btn btn-excel btn-sm hidden" title="Note : Package Name : eg. Kerala amazing"><i class="fa fa-question-circle" style="margin-top:5px;"></i></button>
                            </div>
                            <div class="col-md-3 col-sm-3 mg_bt_10_xs">
                                <input type="text" id="package_code" name="package_code" class="form-control" placeholder="Package Code" title="Package Code" onchange="package_code_check(this.id);" />
                                <button type="button" class="btn btn-excel btn-sm hidden" title="Note : Package Code : eg. Ker001"><i class="fa fa-question-circle" style="margin-top:5px;"></i></button>
                            </div>
                            <div class="col-md-3 col-sm-3 mg_bt_10_xs">
                                <input type="number" id="total_nights" onchange="validate_balance(this.id); calculate_days()" name="total_nights" placeholder="*Nights" title="Total Nights">
                            </div>
                            <div class="col-md-3 col-sm-3 mg_bt_10_xs">
                                <input type="number" id="total_days" onchange=" validate_days('total_nights' , 'total_days');" name="total_days" class="form-control" placeholder="*Days" title="Total Days" readonly />
                            </div>
                            <div class="col-md-3 col-sm-3">
                                <select id="status" name="status" title="Status" class="form-control hidden">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <?php
// Fetch value from app_settings
$sq_app_settings = mysqli_fetch_assoc(mysqlQuery("SELECT b2c_flag FROM app_settings LIMIT 1"));
$b2c_clag = $sq_app_settings['b2c_flag'];
?>
<?php if($b2c_clag == '1'){ ?>
                        <div class="col-xs-12 no-pad mg_bt_20">
                            <div class="col-md-3 col-sm-4 mg_bt_10_xs">
                                <input type="text" id="seo_slug" name="seo_slug" class="form-control" placeholder="*SEO friendly slug" title="SEO friendly slug" readonly />
                                <button type="button" class="btn btn-excel btn-sm hidden" title="Note : SEO friendly slug auto generated from package name : eg. amazing-bangkok-tour-2025"><i class="fa fa-question-circle" style="margin-top:5px;"></i></button>
                            </div>
                            <div class="col-md-3 col-sm-3">
                                <select id="tour_theme" name="tour_theme" title="Select Tour Theme" class="form-control" style="width:100%">
                                    <option value="">Tour Theme</option>
                                    <?php
                                    $sq_query = mysqlQuery("select id,name from tour_theme where status != 'Inactive'");
                                    while ($row_theme = mysqli_fetch_assoc($sq_query)) { ?>
                                        <option value="<?php echo $row_theme['id']; ?>">
                                            <?php echo $row_theme['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="col-xs-12 no-pad mg_bt_20 hidden">
                            <div class="col-md-2 col-sm-3 mg_bt_10_xs">
                                <input type="text" id="adult_cost" name="adult_cost" onchange="validate_balance(this.id);" class="form-control" placeholder="Adult Cost" title="Adult Cost" />
                            </div>
                            <div class="col-md-2 col-sm-3 mg_bt_10_xs">
                                <input type="text" id="child_cost" name="child_cost" onchange="validate_balance(this.id);" class="form-control" placeholder="Child Cost" title="Child Cost" />
                            </div>
                            <div class="col-md-2 col-sm-3 mg_bt_10_xs">
                                <input type="text" id="infant_cost" name="infant_cost" onchange="validate_balance(this.id);" class="form-control" placeholder="Infant Cost" title="Infant Cost" />
                            </div>
                            <div class="col-md-2 col-sm-3 mg_bt_10_xs">
                                <input type="text" id="child_with" name="child_with" onchange="validate_balance(this.id);" class="form-control" placeholder="Child with Bed Cost" title="Child with Bed Cost" />
                            </div>
                            <div class="col-md-2 col-sm-3 mg_bt_10_xs">
                                <input type="text" id="child_without" name="child_without" onchange="validate_balance(this.id);" class="form-control" placeholder="Child w/o Bed Cost" title="Child w/o Bed Cost" />
                            </div>
                            <div class="col-md-2 col-sm-3 mg_bt_10_xs">
                                <input type="text" id="extra_bed" name="extra_bed" onchange="validate_balance(this.id);" class="form-control" placeholder="Extra Bed Cost" title="Extra Bed Cost" />
                            </div>
                        </div>

                          <div class="col-xs-12 no-pad">
                        <div class="ai-chat-container">
                            <div class="touraiToggleBtn_div">
                                <button class="btn btn-info btn-sm" id="touraiToggleBtn" type="button" aria-label="Toggle AI assistant">
                                    <i class="fa fa-magic"></i>&nbsp;AI
                                </button>
                            </div>
                        
                            <div class="ai-chat-box-container">
                                <div class="ai-chat-box" id="aiChatBox" aria-hidden="true">
                                <textarea id="aiMessageInput" placeholder="Type your message..."></textarea>
                                 <button type="button" class="btn btn-sm btn-success send-btn"  id="btnAnalyseMessage"><i class="fa fa-paper-plane-o"></i>&nbsp;&nbsp;Send</button>
                            </div>
                            <div id="aiApiInfo"></div>
                            </div>
                        </div>
                    </div>



                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" id="div_list1"></div>
                </div>

                <div id="aiHotelInfo" class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_20">
                    <legend>Hotel Information</legend>
                    <div class="row mg_bt_20">
                        <div class="col-md-6 mg_tp_10"> <button type="button" class="btn btn-excel btn-sm" title="Note - Please ensure you added city wise hotel & tariff using Supplier Master"><i class="fa fa-question-circle"></i></button>
                            <button type="button" class="btn btn-excel btn-sm hidden" title="Add Hotel" onclick="hotel_save_modal()"><i class="fa fa-plus"></i></button>
                        </div>
                        <div class="col-xs-6 col-md-6 text-right mg_tp_10">
                            <button type="button" class="btn btn-excel" title="Add Row" onclick="addRow('tbl_package_hotel_master');city_lzloading('select[name^=city_name1]');if(typeof initAllHotelSelectAddNew==='function'){initAllHotelSelectAddNew('#tbl_package_hotel_master');}"><i class="fa fa-plus"></i></button>
                            <button type="button" class="btn btn-pdf btn-sm" title="Delete Row" onclick="deleteRow('tbl_package_hotel_master')"><i class="fa fa-trash"></i></button>
                        </div>
                        <div class="col-xs-12">
                            <div class="table-responsive">
                                <style>
                                    #tbl_package_hotel_master td:first-child {
                                        vertical-align: middle !important;
                                        text-align: center;
                                    }
                                    #tbl_package_hotel_master td:first-child .custom_checkbox {
                                        display: inline-block;
                                        margin: 0 auto;
                                        vertical-align: middle;
                                    }
                                </style>
                                <table id="tbl_package_hotel_master" name="tbl_package_hotel_master" class="table mg_bt_0 table-bordered mg_bt_10 pd_bt_51">
                                    <tr>
                                        <td style="vertical-align:middle; text-align:center;">
                                            <div style="display:flex; align-items:center;  height:100%;">
                                                <input id="chk_dest1" name="chk_dest1" type="checkbox" checked class="custom_checkbox">
                                            </div>
                                        </td>
                                        <td><input maxlength="15" value="1" type="text" name="no" placeholder="Sr. No." class="form-control" disabled /></td>
                                        <td style="min-width:160px;"><select id="city_name" name="city_name1" onchange="hotel_name_list_load(this.id);" class="city_master_dropdown app_select2" style="width:100%" title="Select City Name" data-add-new-option="true">
                                            </select>
                                        </td>
                                        <td  style="min-width:180px;"><select id="hotel_name" name="hotel_name1" onchange="hotel_type_load(this.id);" style="width:100%" title="Select Hotel Name" class="app_select2 form-control" data-add-new-option="true">
                                                <option value="">*Hotel Name</option>
                                            </select>
                                        </td>
                                        <td class="col-md-3" style="min-width:140px;"><input type="text" id="hotel_type" name="hotel_type1" class="form-control" placeholder="*Hotel Category" title="Hotel Category" readonly style="width:100%;"></td>
                                        <td class="col-md-2" style="min-width:110px;max-width:130px;"><input type="text" id="hotel_tota_days1" onchange="validate_balance(this.id)" name="hotel_tota_days1" class="form-control" placeholder="*Total Night" title="Total Night" style="width:100%;"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mg_bt_20">
                </div>
                <div id="aiTransportInfo" class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_20">
                    <legend>Transport Information</legend>
                    <div class="row mg_bt_20">
                        <div class="col-md-6 mg_tp_10">
                            <button type="button" class="btn btn-excel btn-sm" title="Note - Please ensure you added transfer tariff"><i class="fa fa-question-circle"></i></button>
                            <button type="button" class="btn btn-excel hidden" title="Add Vehicle" onclick="vehicle_save_modal('<?php echo $vehcile_id_str; ?>')"><i class="fa fa-plus"></i></button>
                            <button type="button" class="btn btn-excel btn-sm" title="Add Airport" onclick="airport_airline_save_modal()"><i class="fa fa-plus"></i></button>
                        </div>
                        <div class="col-xs-6 text-right mg_tp_10">
                            <button type="button" class="btn btn-excel" title="Add Row" onclick="addRow('tbl_package_tour_transport');destinationLoading('select[name^=pickup_from]', 'Pickup Location');destinationLoading('select[name^=drop_to]', 'Drop-off Location');"><i class="fa fa-plus"></i></button>
                            <button type="button" class="btn btn-pdf btn-sm" title="Delete Row" onclick="deleteRow('tbl_package_tour_transport')"><i class="fa fa-trash"></i></button>
                        </div>
                        <div class="col-xs-12">
                            <div class="table-responsive">
                                <table id="tbl_package_tour_transport" name="tbl_package_tour_transport" class="table mg_bt_0 table-bordered mg_bt_10 pd_bt_51">
                                    <tbody>
                                        <tr>
                                            <td class="col-md-1" style="vertical-align:middle; text-align:center;"><div style="display:flex; align-items:center;  height:100%;"><input class="css-checkbox labelauty" id="chk_transport1" type="checkbox" checked="" autocomplete="off" data-original-title="" title="" aria-hidden="true" style="display: none;"><label for="chk_transport1"><span class="labelauty-unchecked-image"></span><span class="labelauty-checked-image"></span></label><label class="css-label" for="chk_transport1"> </label></div></td>

                                            <td class="col-md-1"><input maxlength="15" value="1" type="text" name="username" placeholder="Sr No." class="form-control" disabled="" autocomplete="off" data-original-title="" title=""></td>
                                            <td class="col-md-3"><select name="vehicle_name1" id="vehicle_name1" title="Select Vehicle" style="width:100%" class="form-control app_select2" data-add-new-option="true">
                                                    <option value="">Select Vehicle</option>
                                                    <?php
                                                    $sq_query = mysqlQuery("select * from b2b_transfer_master where status != 'Inactive'");
                                                    while ($row_dest = mysqli_fetch_assoc($sq_query)) { ?>
                                                        <option value="<?php echo $row_dest['entry_id']; ?>">
                                                            <?php echo $row_dest['vehicle_name']; ?></option>
                                                    <?php } ?>
                                                </select></td>
                                            <td class="col-md-3"><select name="pickup_from" id="pickup_from" data-toggle="tooltip" style="width:100%;" title="Pickup Location" class="form-control app_minselect2">
                                                </select></td>
                                            <td class="col-md-3"><select name="drop_to" id="drop_to" style="width:100%;" data-toggle="tooltip" title="Drop-off Location" class="form-control app_minselect2">
                                                </select></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mg_bt_20">
                    <div class="col-md-12 col-sm-6 mg_bt_10_sm_xs">
                        <textarea class="form-control" id="note" name="note" placeholder="Note" title="Note" rows="2"></textarea>
                    </div>
                </div>
                <div class="row mg_bt_20">
                    <div class="col-md-6 col-sm-6 mg_bt_10_sm_xs">
                        <h3 class="editor_title">Inclusions</h3>
                        <textarea class="feature_editor" id="inclusions" name="inclusions" placeholder="Inclusions" title="Inclusions" rows="4"></textarea>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h3 class="editor_title">Exclusions</h3>
                        <textarea class="feature_editor" id="exclusions" name="exclusions" placeholder="Exclusions" title="Exclusions" rows="4"></textarea>
                    </div>
                </div>
                <div class="panel panel-default main_block bg_light pad_8 text-center mg_bt_0" style="background-color: #fff; border: none;">
                    <button class="btn btn-sm btn-success" id="btn_save1"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
                </div>
            </div>
        </div>
    </div>
    
    <input type="hidden" id="base_url" value="<?= BASE_URL ?>" />

</form>

<!-- Itinerary Modal Container (outside form to avoid nested forms / duplicate field IDs) -->
<div id="div_itinerary_modal"></div>
<div id="div_modal_content"></div>


<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>
<script src="<?php echo BASE_URL ?>js/app/field_validation.js"></script>

<script>
    var packageHotelLoadUrl = $('#base_url').val() + 'view/custom_packages/master/package/hotel/hotel_name_load.php';
    if (typeof hotelSupplierQuickLoadUrl === 'undefined') {
        hotelSupplierQuickLoadUrl = packageHotelLoadUrl;
    }
    $('#dest_name_s,#vehicle_name1,#currency_code,#dest_image').select2();
    if (typeof initAllDestinationSelectAddNew === 'function') {
        initAllDestinationSelectAddNew('#frm_package_master_save');
    }
    if (typeof initAllVehicleSelectAddNew === 'function') {
        initAllVehicleSelectAddNew('#frm_package_master_save');
    }
   
    city_lzloading('select[name^="city_name1"]');
    destinationLoading('select[name^="pickup_from"]', "Pickup Location");
    destinationLoading('select[name^="drop_to"]', "Drop-off Location");

   // Initialize Select2
$('#hotel_name').select2({
    width: '100%',
    minimumResultsForSearch: 0,
    dropdownParent: $('body')
});
captureHotelSelect2Config($('#hotel_name'));
initHotelSelectAddNew('#hotel_name');

$(function () {
    initAllHotelSelectAddNew('#tbl_package_hotel_master');
    setTimeout(function () { initAllHotelSelectAddNew('#tbl_package_hotel_master'); }, 400);
});

    // --------------------------------------
    // $('#dest_name_s').on('change', function() {
    //     let fullText = $(this).find('option:selected').text().replace(/\s+/g, '');
    //     let firstLetters = fullText.substring(0, 4); 
    //     let camelCase = firstLetters.charAt(0).toUpperCase() + firstLetters.slice(1).toLowerCase();
    //     $('#package_code').val(camelCase); 
    // });
    // --------------------------------------  jQuery end here

    window.packageFormGeneration = window.packageFormGeneration || 0;
    window.packageSkipDestReset = false;

    function destroySelect2In($scope) {
        $scope.find('select').each(function () {
            var $el = $(this);
            if ($el.data('select2')) {
                try { $el.select2('destroy'); } catch (e) {}
            }
        });
    }

    function resetHotelTableToSingleRow() {
        var $table = $('#tbl_package_hotel_master');
        if (!$table.length) {
            return;
        }
        destroySelect2In($table);
        $table.html(
            '<tr>' +
                '<td style="vertical-align:middle; text-align:center;"><div style="display:flex; align-items:center; justify-content:center; height:100%;"><input id="chk_dest1" name="chk_dest1" type="checkbox" checked class="custom_checkbox"></div></td>' +
                '<td><input maxlength="15" value="1" type="text" name="no" placeholder="Sr. No." class="form-control" disabled /></td>' +
                '<td style="min-width:160px;"><select id="city_name" name="city_name1" onchange="hotel_name_list_load(this.id);" class="city_master_dropdown app_select2" style="width:100%" title="Select City Name" data-add-new-option="true"></select></td>' +
                '<td class="col-md-4" style="min-width:180px;"><select id="hotel_name" name="hotel_name1" onchange="hotel_type_load(this.id);" style="width:100%" title="Select Hotel Name" class="app_select2 form-control" data-add-new-option="true"><option value="">*Hotel Name</option></select></td>' +
                '<td class="col-md-3" style="min-width:140px;"><input type="text" id="hotel_type" name="hotel_type1" class="form-control" placeholder="*Hotel Category" title="Hotel Category" readonly style="width:100%;"></td>' +
                '<td class="col-md-2" style="min-width:110px;max-width:130px;"><input type="text" id="hotel_tota_days1" onchange="validate_balance(this.id)" name="hotel_tota_days1" class="form-control" placeholder="*Total Night" title="Total Night" style="width:100%;"></td>' +
            '</tr>'
        );
        city_lzloading('select[name^="city_name1"]');
        $('#hotel_name').select2({
            width: '100%',
            minimumResultsForSearch: 0,
            dropdownParent: $('body')
        });
        if (typeof captureHotelSelect2Config === 'function') {
            captureHotelSelect2Config($('#hotel_name'));
        }
        if (typeof initHotelSelectAddNew === 'function') {
            initHotelSelectAddNew('#hotel_name');
        }
        if (typeof initAllHotelSelectAddNew === 'function') {
            initAllHotelSelectAddNew('#tbl_package_hotel_master');
        }
    }

    function resetTransportTableToSingleRow() {
        var $table = $('#tbl_package_tour_transport');
        if (!$table.length) {
            return;
        }
        var savedVehicleHtml = '<option value="">Select Vehicle</option>';
        var $existingVehicle = $table.find('select[name^="vehicle_name"]').first();
        if ($existingVehicle.length) {
            // clone options without current selection
            savedVehicleHtml = $existingVehicle.html();
        }
        destroySelect2In($table);
        $table.html(
            '<tbody><tr>' +
                '<td class="col-md-1"><input id="chk_transport1" type="checkbox" checked class="custom_checkbox"></td>' +
                '<td class="col-md-1"><input maxlength="15" value="1" type="text" name="username" placeholder="Sr No." class="form-control" disabled></td>' +
                '<td class="col-md-3"><select name="vehicle_name1" id="vehicle_name1" title="Select Vehicle" style="width:100%" class="form-control app_select2" data-add-new-option="true">' + savedVehicleHtml + '</select></td>' +
                '<td class="col-md-3"><select name="pickup_from" id="pickup_from" data-toggle="tooltip" style="width:100%;" title="Pickup Location" class="form-control app_minselect2"></select></td>' +
                '<td class="col-md-3"><select name="drop_to" id="drop_to" style="width:100%;" data-toggle="tooltip" title="Drop-off Location" class="form-control app_minselect2"></select></td>' +
            '</tr></tbody>'
        );
        $('#vehicle_name1').val('').trigger('change');
        if (!$('#vehicle_name1').data('select2')) {
            $('#vehicle_name1').select2({ width: '100%' });
        }
        if (typeof initAllVehicleSelectAddNew === 'function') {
            initAllVehicleSelectAddNew('#tbl_package_tour_transport');
        }
        destinationLoading('select[name^="pickup_from"]', 'Pickup Location');
        destinationLoading('select[name^="drop_to"]', 'Drop-off Location');
    }

    function resetPackageDependentSections(reason) {
        if (window.packageSkipDestReset) {
            return;
        }
        window.packageFormGeneration = (window.packageFormGeneration || 0) + 1;
        window.packageCreateImages = {};
        window.selectedItineraryImage = null;
        $('#div_itinerary_modal').empty();
        $('#aiApiInfo').text('');

        var totalNights = $('#total_nights').val();
        if (totalNights !== '' && !isNaN(parseInt(totalNights, 10)) && parseInt(totalNights, 10) >= 0) {
            calculate_days();
        } else {
            $('#div_list1').html('');
            $('#total_days').val('');
        }

        resetHotelTableToSingleRow();
        resetTransportTableToSingleRow();
        console.log('PACKAGE CREATE: Reset dependent sections after', reason || 'destination change', 'generation=', window.packageFormGeneration);
    }

    $('#dest_name_s').off('change.packageDestReset').on('change.packageDestReset', function () {
        var dest_id = $(this).val();
        if (dest_id === '') {
            return;
        }
        if (!window.packageSkipDestReset) {
            resetPackageDependentSections('destination-change');
        }
        $.post('get_package_code.php', { dest_id: dest_id }, function (data) {
            $('#package_code').val(data.trim());
            package_code_check('package_code');
        });
    });


    function generateSlug(packageName) {
        if (!packageName) return '';

        return packageName
            .toLowerCase()
            .trim()
            .replace(/\s+/g, '-')
            .replace(/[^a-z0-9-]/g, '')
            .replace(/--+/g, '-');
    }

    function updateSlug() {
      
        const packageNameVal = $('#package_name').val();
        if (packageNameVal) {
            $('#seo_slug').val(generateSlug(packageNameVal));
        }
    }

    function get_dest_image(dest_id) {

        var dest_id = $("#" + dest_id).val();
        $.post('image_list_reflect.php', {
            dest_id: dest_id
        }, function(data) {
            $('#dest_image').html(data);
        });
    }

    function generate_list() {
        console.log("GENERATE_LIST: Function called");
        var total_days = $("#total_days").val();
        console.log("GENERATE_LIST: Total days =", total_days);
        $.post('generate_program_list.php', {
            total_days: total_days
        }, function(data) {
            console.log("GENERATE_LIST: Response received, setting HTML");
            $('#div_list1').html(data);
        });
    }

    function calculate_days() {
        var total_nights = $("#total_nights").val();
        var days = parseInt(total_nights) + 1;
        $("#total_days").val(days);
        generate_list();
    }

    function package_name_check(package_name) {
        var package_name1 = $('#' + package_name).val();

        $.post("../package_name_check.php", {
            package_name: package_name1
        }, function(data) {
            if (data == 'This package name already exists.') {
                error_msg_alert(data);
                return false;
            } else {
                return true;
            }
        });
    }

    function package_code_check(package_code) {

        var package_code1 = $('#' + package_code).val();
        $.post("../package_code_check.php", {
            package_code: package_code1
        }, function(data) {
            if (data == 'This package code already exists.') {
                error_msg_alert(data);
                return false;
            } else {
                return true;
            }
        });
    }

    function table_info_validate() {

        g_validate_status = true;
        var validate_message = "";

        //Special attraction table
        var table = document.getElementById("dynamic_table_list");
        if (!table) {
            error_msg_alert('Please enter total nights to generate itinerary rows.');
            g_validate_status = false;
            return false;
        }
        var rowCount = table.rows.length;

        for (var i = 0; i < rowCount; i++) {

            var row = table.rows[i];
            var itineraryData = getPackageItineraryRowData(row);
            validate_dynamic_empty_fields(row.querySelector('input[name="special_attaraction"]'));
            validate_dynamic_empty_fields(row.querySelector('textarea[name="day_program"]'));
            validate_dynamic_empty_fields(row.querySelector('input[name="overnight_stay"]'));

            var attractionInput = row.querySelector('input[name="special_attaraction"]');
            var programInput = row.querySelector('textarea[name="day_program"]');
            var stayInput = row.querySelector('input[name="overnight_stay"]');
            var flag1 = attractionInput ? validate_spattration(attractionInput.id) : true;
            var flag2 = programInput ? validate_dayprogram(programInput.id) : true;
            var flag3 = stayInput ? validate_onstay(stayInput.id) : true;
            if (!flag1 || !flag2 || !flag3) {
                return false;
            }
        }
        var service_tax = $('#service_tax').val();
        if (service_tax === '') {
            error_msg_alert('Select Tax(%)!');
            return false;
        }
        //Hotel info table
        var total_nights = $('#total_nights').val();
        var table = document.getElementById("tbl_package_hotel_master");
        var rowCount = table.rows.length;

        for (var i = 0; i < rowCount; i++) {

            var row = table.rows[i];
            if (rowCount == 1) {
                if (!row.cells[0].childNodes[0].checked) {
                    error_msg_alert("Atleast One Hotel is required!");
                    g_validate_status = false;
                    return false;
                }
            }

            if (row.cells[0].childNodes[0].checked) {

                var hotelData = getPackageHotelRowData(row);
                validate_dynamic_empty_fields(row.querySelector('select[name^="city_name"]'));
                validate_dynamic_empty_fields(row.querySelector('select[name^="hotel_name"]'));
                validate_dynamic_empty_fields(row.querySelector('input[name^="hotel_type"]'));
                validate_dynamic_empty_fields(row.querySelector('input[name^="hotel_tota_days"]'));

                if (hotelData.city_name == "") {
                    validate_message += "Enter City Name in row-" + (i + 1) + "<br>";
                }
                if (hotelData.hotel_name == "") {
                    validate_message += "Enter Hotel Name in row-" + (i + 1) + "<br>";
                }
                if (hotelData.hotel_type == "") {
                    validate_message += "Enter Hotel Type in row-" + (i + 1) + "<br>";
                }
                if (hotelData.total_days == "") {
                    validate_message += "Enter Total Nights in row-" + (i + 1) + "<br>";
                }
            }
        }
        g_validate_status = true;

        //Transport info table
        var table = document.getElementById("tbl_package_tour_transport");
        var rowCount = table.rows.length;
        for (var i = 0; i < rowCount; i++) {
            var row = table.rows[i];
            if (row.cells[0].childNodes[0].checked) {

                validate_dynamic_empty_fields(row.cells[2].childNodes[0]);
                validate_dynamic_empty_fields(row.cells[3].childNodes[0]);
                validate_dynamic_empty_fields(row.cells[4].childNodes[0]);

                if (row.cells[2].childNodes[0].value == "") {
                    validate_message += "Enter Vehicle Name in row-" + (i + 1) + "<br>";
                }
                if (row.cells[3].childNodes[0].value == "") {
                    validate_message += "Enter Vehicle pickup in row-" + (i + 1) + "<br>";
                }
                if (row.cells[4].childNodes[0].value == "") {
                    validate_message += "Enter Vehicle drop in row-" + (i + 1) + "<br>";
                }
            }
        }
        if (validate_message != "") {
            $('#site_alert').vialert({
                type: "error",
                message: validate_message,
                delay: 10000,
            });
        }
        if (g_validate_status == false) {
            return false;
        }
    }

    function load_images(hotel_names) {
        $.ajax({
            type: 'post',
            url: 'get_hotel_img.php',
            data: {
                hotel_name: hotel_names
            },
            success: function(result) {

                $('#images_list').html(result);
            }
        });
    }

    function delete_image(image_id, hotel_name) {

        var base_url = $("#base_url").val();
        $.ajax({
            type: 'post',
            url: base_url + 'controller/custom_packages/delete_hotel_image.php',
            data: {
                image_id: image_id
            },
            success: function(result) {
                msg_alert(result);
                load_images(hotel_name);
            }
        });
    }

    /**Hotel Name load start**/

    function hotel_name_list_load(id) {

        var city_id = $("#" + id).val();
        if (!city_id) {
            return;
        }

        var count = id.substring(9);
        var $hotel = $("#hotel_name" + count);
        if (!$hotel.length) {
            $hotel = $('#hotel_name');
        }

        if (typeof hotelDropdownLoadByCity === 'function') {
            hotelDropdownLoadByCity(city_id, $hotel);
            return;
        }

        $.get(packageHotelLoadUrl, {
            city_id: city_id
        }, function(data) {
            if ($hotel.data('select2')) {
                $hotel.select2('destroy');
            }
            $hotel.html(data);
            $hotel.select2({
                width: '100%',
                minimumResultsForSearch: 0,
                dropdownParent: $('body')
            });
            captureHotelSelect2Config($hotel);
            initHotelSelectAddNew($hotel);
        });

    }

    function hotel_type_load(id) {

        var hotel_id = $("#" + id).val();
        var match = String(id || '').match(/^hotel_name(\d*)$/);
        var count = match ? match[1] : '';

        $.get("hotel/hotel_type_load.php", {
            hotel_id: hotel_id
        }, function(data) {

            $("#hotel_type" + count).val(data);

        });

    }

    function getPackageHotelRowElements(row) {
        var $row = $(row);
        return {
            $citySelect: $row.find('select[name^="city_name"]'),
            $hotelSelect: $row.find('select[name^="hotel_name"]'),
            $hotelType: $row.find('input[name^="hotel_type"]'),
            $totalNights: $row.find('input[name^="hotel_tota_days"]')
        };
    }

    function getPackageItineraryRowData(row) {
        var $row = $(row);
        return {
            special_attaraction: $.trim($row.find('input[name="special_attaraction"]').first().val() || ''),
            day_program: $.trim($row.find('textarea[name="day_program"]').first().val() || ''),
            overnight_stay: $.trim($row.find('input[name="overnight_stay"]').first().val() || ''),
            meal_plan: $row.find('select[name="meal_plan"]').first().val() || ''
        };
    }

    function getPackageHotelRowData(row) {
        var els = getPackageHotelRowElements(row);
        return {
            city_name: $.trim(els.$citySelect.val() || ''),
            hotel_name: $.trim(els.$hotelSelect.val() || ''),
            hotel_type: $.trim(els.$hotelType.val() || ''),
            total_days: $.trim(els.$totalNights.val() || '')
        };
    }

    function resetPackageSaveState() {
        window.packageSaveInProgress = false;
        var $btn = $('#btn_save1');
        if ($btn.length) {
            $btn.prop('disabled', false);
            try { $btn.button('reset'); } catch (e) {}
            $btn.val('Save');
        }
    }

    $(function() {
        window.packageSaveInProgress = false;

        $('#frm_package_master_save').validate({

            rules: {
                dest_name_s: {
                    required: true
                },
                package_name: {
                    required: true
                },
                total_days: {
                    required: true,
                    number: true
                },
                total_nights: {
                    required: true,
                    number: true
                },
                day_program: {
                    required: true
                },
            },

            invalidHandler: function() {
                resetPackageSaveState();
            },

            submitHandler: function(form) {
                console.log("FORM SUBMIT: Form submission started");
                
                // Prevent double submission
                if (window.packageSaveInProgress) {
                    console.log("FORM SUBMIT: Already in progress, preventing double submission");
                    return false;
                }
                
                // Clear itinerary picker DOM so its rows never leak into package save
                $('#div_itinerary_modal').empty();
                
                var base_url = $('#base_url').val();

                var dest_id = $("#dest_name_s").val();

                var currency_id = $('#currency_code').val();

                var dest_image = $('#dest_image').val();
                var taxation_type = $('#taxation_type').val();
                var taxation_id = $('#taxation_id').val();
                var service_tax = $('#service_tax').val();

                var package_code = $("#package_code").val();

                var package_name = $("#package_name").val();
                var result = package_name_validation('package_name');
                if (!result) {
                    error_msg_alert('Package name should not allow special character.');
                    return false;
                }
                var total_days1 = $("#total_days").val();

                var total_nights = $("#total_nights").val();
                var adult_cost = $("#adult_cost").val();
                var child_cost = $("#child_cost").val();
                var infant_cost = $("#infant_cost").val();
                var child_with = $("#child_with").val();
                var child_without = $("#child_without").val();
                var extra_bed = $("#extra_bed").val();
                var status = $("#status").val();
                var note = $('#note').val();

                var seo_slug = $('#seo_slug').val();
                var tour_theme = $('#tour_theme').val();

                var iframe = document.getElementById("inclusions-wysiwyg-iframe");
                var inclusions = iframe.contentWindow.document.body.innerHTML;
                var iframe1 = document.getElementById("exclusions-wysiwyg-iframe");
                var exclusions = iframe1.contentWindow.document.body.innerHTML;
                //Daywise program 

                var day_program_arr = new Array();
                var special_attaraction_arr = new Array();
                var overnight_stay_arr = new Array();
                var meal_plan_arr = new Array();
                var day_image_arr = new Array();

                var table = document.querySelector('#div_list1 #dynamic_table_list') || document.getElementById("dynamic_table_list");
                if (!table || !table.rows.length) {
                    error_msg_alert('Please enter total nights to generate itinerary rows.');
                    return false;
                }
                var expectedDays = parseInt(total_days1, 10) || 0;
                var rowCount = table.rows.length;
                if (expectedDays > 0 && rowCount !== expectedDays) {
                    error_msg_alert('Itinerary rows (' + rowCount + ') must match Total Days (' + expectedDays + '). Please re-enter nights.');
                    return false;
                }
                console.log("PACKAGE SAVE: Table found with", rowCount, "rows");
                for (var i = 0; i < rowCount; i++) {
                    var row = table.rows[i];
                    var itineraryData = getPackageItineraryRowData(row);
                    var special_attaraction = itineraryData.special_attaraction;
                    var day_program = itineraryData.day_program;
                    var overnight_stay = itineraryData.overnight_stay;
                    var meal_plan = itineraryData.meal_plan;

                    if (special_attaraction == "") {
                        error_msg_alert('Special attraction is mandatory in row' + (i + 1));
                        return false;
                    }
                    if (day_program == "") {
                        error_msg_alert('Daywise program is mandatory in row' + (i + 1));
                        return false;
                    }
                    if (overnight_stay == "") {
                        error_msg_alert('Overnight stay is mandatory in row' + (i + 1));
                        return false;
                    }
                    day_program_arr.push(day_program);
                    special_attaraction_arr.push(special_attaraction);
                    overnight_stay_arr.push(overnight_stay);
                    meal_plan_arr.push(meal_plan);
                    
                    // Get image data for this row
                    var img = '';
                    var rowIndex = i + 1; // Convert to 1-based index
                    
                    console.log("PACKAGE SAVE: Processing image for row", i, "with rowIndex", rowIndex);
                    
                    // Check if we have a new image uploaded
                    if (window.packageCreateImages && window.packageCreateImages[rowIndex]) {
                        var imageData = window.packageCreateImages[rowIndex];
                        console.log("PACKAGE SAVE: Found image data for rowIndex", rowIndex, imageData);
                        
                        if (imageData.file && !imageData.uploaded) {
                            console.log("PACKAGE SAVE: Uploading new image for rowIndex", rowIndex);
                            // Upload the image immediately
                            var formData = new FormData();
                            formData.append('uploadfile', imageData.file);
                            
                            $.ajax({
                                url: base_url + 'view/other_masters/itinerary/upload_itinerary_image.php',
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                async: false, // Make it synchronous for data collection
                                success: function(response) {
                                    try {
                                        var msg = response.split('--');
                                        if (msg[0] !== "error" && !/<\/?(html|body|h1|p|address|hr)/i.test(response)) {
                                            img = response;
                                            window.packageCreateImages[rowIndex].uploaded = true;
                                            window.packageCreateImages[rowIndex].image_url = response;
                                            console.log("PACKAGE SAVE: Image uploaded successfully for rowIndex", rowIndex, ":", img);
                                        } else {
                                            console.log("PACKAGE SAVE: Upload failed for rowIndex", rowIndex, ":", response);
                                        }
                                    } catch(e) {
                                        console.log('PACKAGE SAVE: Upload parse error for rowIndex', rowIndex, ':', e);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.log("PACKAGE SAVE: Upload error for rowIndex", rowIndex, ":", error);
                                }
                            });
                        } else if (imageData.image_url) {
                            img = imageData.image_url;
                            console.log("PACKAGE SAVE: Using existing image URL for rowIndex", rowIndex, ":", img);
                        }
                    } else {
                        // Check hidden input for image path
                        var imgPathInput = row.querySelector('input[id="day_image_path_' + rowIndex + '"]');
                        img = imgPathInput ? imgPathInput.value : '';
                        console.log("PACKAGE SAVE: Using hidden input for rowIndex", rowIndex, ":", img);
                    }
                    
                    console.log("PACKAGE SAVE: Final image for row", i, ":", img);
                    day_image_arr.push(img || '');
                }

                //Hotel information
                var total_night = 0;
                var city_name_arr = new Array();

                var hotel_name_arr = new Array();

                var hotel_type_arr = new Array();

                var total_days_arr = new Array();

                var table = document.getElementById("tbl_package_hotel_master");

                var rowCount = table.rows.length;
                var count = 0;
                for (var i = 0; i < rowCount; i++) {

                    var row = table.rows[i];
                    var hotelChecked = row.querySelector('input[type="checkbox"]');
                    if (hotelChecked && hotelChecked.checked) {
                        count++;
                        var hotelData = getPackageHotelRowData(row);
                        var city_name = hotelData.city_name;
                        var hotel_name = hotelData.hotel_name;
                        var hotel_type = hotelData.hotel_type;
                        var total_days = hotelData.total_days;

                        if (city_name == '') {
                            error_msg_alert("City Name is required");
                            return false;
                        }
                        if (hotel_name == '') {
                            error_msg_alert("Hotel Name is required");
                            return false;
                        }
                        if (hotel_type == '') {
                            error_msg_alert("Hotel Type is required");
                            return false;
                        }
                        if (total_days == '') {
                            error_msg_alert("Total nights is required");
                            return false;
                        }

                        city_name_arr.push(city_name);

                        hotel_name_arr.push(hotel_name);

                        hotel_type_arr.push(hotel_type);

                        total_days_arr.push(total_days);

                    }

                }
                if (parseInt(count) == 0) {
                    error_msg_alert("Atleast one hotel is required!");
                    return false;
                }
                //Transport information
                var vehicle_name_arr = new Array();
                var drop_arr = new Array();
                var drop_type_arr = new Array();
                var pickup_arr = new Array();
                var pickup_type_arr = new Array();
                var pickup_type = '';
                var pickup = '';
                var drop_type = '';
                var drop = '';
                var table = document.getElementById("tbl_package_tour_transport");
                var rowCount = table.rows.length;
                for (var i = 0; i < rowCount; i++) {
                    var row = table.rows[i];
                    var transportChecked = row.querySelector('input[type="checkbox"]');
                    if (transportChecked && transportChecked.checked) {


                        var pickupSelect = row.querySelector('select[name^="pickup_from"]');
                        var dropSelect = row.querySelector('select[name^="drop_to"]');
                        var vehicleSelect = row.querySelector('select[name^="vehicle_name"]');
                        pickup = pickupSelect ? (pickupSelect.value || '') : '';
                        drop = dropSelect ? (dropSelect.value || '') : '';
                        pickup_type = pickupSelect && pickup ? ($("option:selected", pickupSelect).parent().attr('value') || '') : '';
                        drop_type = dropSelect && drop ? ($("option:selected", dropSelect).parent().attr('value') || '') : '';

                        var vehicle_name = vehicleSelect ? (vehicleSelect.value || '') : '';
                        if (vehicle_name == "") {
                            error_msg_alert('Transport Vehicle is mandatory in row' + (i + 1));
                            return false;
                        }
                        if (pickup == "") {
                            error_msg_alert('Transport pickup location is mandatory in row' + (i + 1));
                            return false;
                        }
                        if (drop == "") {
                            error_msg_alert('Transport drop location is mandatory in row' + (i + 1));
                            return false;
                        }

                        vehicle_name_arr.push(vehicle_name);
                        pickup_arr.push(pickup);
                        pickup_type_arr.push(pickup_type);
                        drop_arr.push(drop);
                        drop_type_arr.push(drop_type);
                    }
                }

                var tour_type = $('#tour_type').val();
                window.packageSaveInProgress = true;
                $('#btn_save1').button('loading');
                $("#vi_confirm_box").vi_confirm_box({

                    callback: function(result) {

                        if (result == "yes") {

                            console.log("FORM SUBMIT: Disabling save button");
                            $("#btn_save1").prop("disabled", true);
                            $("#btn_save1").val('Saving...');

                            $.post(base_url +
                                "controller/custom_packages/package_master_save.php",

                                {
                                    tour_type: tour_type,
                                    dest_id: dest_id,
                                    currency_id: currency_id,
                                    taxation_type: taxation_type,
                                    taxation_id: taxation_id,
                                    service_tax: service_tax,
                                    package_code: package_code,
                                    package_name: package_name,
                                    seo_slug: seo_slug,
                                    tour_theme:tour_theme,
                                    total_days: total_days1,
                                    total_nights: total_nights,
                                    status: status,
                                    city_name_arr: city_name_arr,
                                    hotel_name_arr: hotel_name_arr,
                                    hotel_type_arr: hotel_type_arr,
                                    total_days_arr: total_days_arr,
                                    vehicle_name_arr: vehicle_name_arr,
                                    drop_arr: drop_arr,
                                    drop_type_arr: drop_type_arr,
                                    pickup_arr: pickup_arr,
                                    pickup_type_arr: pickup_type_arr,
                                    child_cost: child_cost,
                                    adult_cost: adult_cost,
                                    infant_cost: infant_cost,
                                    child_with: child_with,
                                    child_without: child_without,
                                    extra_bed: extra_bed,
                                    inclusions: inclusions,
                                    exclusions: exclusions,
                                    day_program_arr: day_program_arr,
                                    special_attaraction_arr: special_attaraction_arr,
                                    overnight_stay_arr: overnight_stay_arr,
                                    meal_plan_arr: meal_plan_arr,
                                    day_image_arr: day_image_arr,
                                    note: note,
                                    dest_image: dest_image
                                },

                                function(data) {
                                    console.log("FORM SUBMIT: Response received");
                                    resetPackageSaveState();
                                    var msg = data.split('--');
                                    if (msg[0] == "error") {
                                        error_msg_alert(msg[1]);
                                        return false;
                                    } else {
                                        booking_save_message(data);
                                    }
                                }).fail(function() {
                                    console.log("FORM SUBMIT: AJAX failed");
                                    resetPackageSaveState();
                                });
                        } else {
                            console.log("FORM SUBMIT: User cancelled, resetting flag");
                            resetPackageSaveState();
                        }
                    }

                });
            }
        });
    });

    function booking_save_message(data) {
        var base_url = $("#base_url").val();
        $('#vi_confirm_box').vi_confirm_box({
            false_btn: false,
            message: data,
            true_btn_text: 'Ok',
            callback: function(data1) {
                if (data1 == "yes") {
                    update_b2c_cache();
                    window.location.href = '../index.php';
                }
            }
        });
    }

    (function ($) {
        var aiToggleBtn = document.getElementById('touraiToggleBtn');
        var aiChatBox = document.getElementById('aiChatBox');

        if (aiToggleBtn && aiChatBox) {
            aiToggleBtn.addEventListener('click', function () {
                aiChatBox.classList.toggle('show');
                var isVisible = aiChatBox.classList.contains('show');
                aiChatBox.setAttribute('aria-hidden', String(!isVisible));
            });
        }

        function cleanSimilarSuffix(text) {
            return String(text || '').replace(/\s*\/\s*similar/gi, '').trim();
        }

        function normalizeAiList(items) {
            if (Array.isArray(items)) {
                return items.filter(function (item) {
                    return item !== null && item !== undefined && String(item).trim() !== '';
                });
            }
            if (typeof items === 'string' && items.trim() !== '') {
                return [items.trim()];
            }
            if (items && typeof items === 'object') {
                return Object.values(items).filter(function (item) {
                    return item !== null && item !== undefined && String(item).trim() !== '';
                });
            }
            return [];
        }

        function escapeHtml(text) {
            return $('<div/>').text(text || '').html();
        }

        function buildListHtml(items) {
            var list = normalizeAiList(items);
            if (!list.length) {
                return '';
            }
            return '<ul>' + list.map(function (item) {
                return '<li>' + escapeHtml(item) + '</li>';
            }).join('') + '</ul>';
        }

        function setWysiwygContent(textareaId, items) {
            var html = buildListHtml(items);
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

        function showAiStatus(message) {
            $('#aiApiInfo').text(message || '');
        }

        function getHotelRowElements(row) {
            return getPackageHotelRowElements(row);
        }

        function getTransportRowElements(row) {
            var $row = $(row);
            return {
                $vehicleSelect: $row.find('select[name^="vehicle_name"]'),
                $pickupSelect: $row.find('select[name^="pickup_from"]'),
                $dropSelect: $row.find('select[name^="drop_to"]')
            };
        }

        function matchHotelInSelect($hotelSelect, hotelName) {
            var search = cleanSimilarSuffix(hotelName);
            if (!$hotelSelect.length || !search) {
                return '';
            }
            var bestVal = '';
            var bestScore = 0;
            $hotelSelect.find('option').each(function () {
                var val = $(this).val();
                var text = $(this).text();
                if (!val) {
                    return;
                }
                var score = fuzzyMatchScore(search, text);
                if (score > bestScore) {
                    bestScore = score;
                    bestVal = val;
                }
            });
            return bestScore >= 30 ? bestVal : '';
        }

        function isValidLocationName(name) {
            var cleaned = cleanSimilarSuffix(name);
            return cleaned && cleaned !== '()' && cleaned !== '() ()';
        }

        function selectMealPlan($mealSelect, mealValue) {
            if (!$mealSelect || !$mealSelect.length) {
                return;
            }
            if (!mealValue) {
                $mealSelect.val('');
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
            }
        }

        function stripPasteContent(text) {
            var value = String(text || '').trim();
            if (!value) {
                return '';
            }
            if (value.indexOf('<') !== -1 && value.indexOf('>') !== -1) {
                var tmp = document.createElement('div');
                tmp.innerHTML = value;
                value = tmp.textContent || tmp.innerText || value;
            }
            return value.replace(/\u00a0/g, ' ').replace(/\s+\n/g, '\n').trim();
        }

        function fuzzyMatchScore(needle, haystack) {
            needle = cleanSimilarSuffix(String(needle || '')).toLowerCase();
            haystack = String(haystack || '').toLowerCase();
            if (!needle || !haystack) {
                return 0;
            }
            if (haystack === needle) {
                return 100;
            }
            if (haystack.indexOf(needle) >= 0 || needle.indexOf(haystack) >= 0) {
                return 80;
            }
            var needleParts = needle.split(/[\s,–-]+/).filter(Boolean);
            var hits = 0;
            needleParts.forEach(function (part) {
                if (part.length > 2 && haystack.indexOf(part) >= 0) {
                    hits++;
                }
            });
            return hits > 0 ? 50 + hits * 5 : 0;
        }

        var aiResolveUrl = $('#base_url').val() + 'view/custom_packages/master/package/ai_resolve_masters.php';

        function buildLocationSearchTerms(name) {
            var terms = [];
            var search = cleanSimilarSuffix(name);
            if (!search) {
                return terms;
            }
            terms.push(search);
            search.split(/\s+to\s+/i).forEach(function (part) {
                part = cleanSimilarSuffix(part);
                if (part && terms.indexOf(part) === -1) {
                    terms.push(part);
                }
            });
            search.replace(/\s*\([^)]*\)\s*/g, ' ').trim().split(/[\s,]+/).forEach(function (part) {
                if (part.length > 2 && terms.indexOf(part) === -1) {
                    terms.push(part);
                }
            });
            return terms;
        }

        function resolveMaster(action, term, extraData) {
            return $.ajax({
                type: 'post',
                url: aiResolveUrl,
                dataType: 'json',
                data: $.extend({ action: action, term: term }, extraData || {})
            });
        }

        function lookupCity(cityName, extraTerms) {
            var deferred = $.Deferred();
            var terms = buildLocationSearchTerms(cityName);
            (extraTerms || []).forEach(function (term) {
                buildLocationSearchTerms(term).forEach(function (part) {
                    if (part && terms.indexOf(part) === -1) {
                        terms.push(part);
                    }
                });
            });
            if (!terms.length) {
                deferred.resolve(null);
                return deferred.promise();
            }

            function tryTerm(index) {
                if (index >= terms.length) {
                    deferred.resolve(null);
                    return;
                }
                resolveMaster('city', terms[index]).done(function (item) {
                    if (item && item.id) {
                        deferred.resolve(item);
                    } else {
                        tryTerm(index + 1);
                    }
                }).fail(function () {
                    tryTerm(index + 1);
                });
            }

            tryTerm(0);
            return deferred.promise();
        }

        function getHotelCityFallbacks(parsed) {
            var list = [];
            var itinerary = parsed && parsed.itinerary ? parsed.itinerary : {};
            normalizeAiList(itinerary.destination).forEach(function (dest) {
                if (list.indexOf(dest) === -1) {
                    list.push(dest);
                }
            });
            (itinerary.detailed_program || []).forEach(function (day) {
                if (day && day.overnight_stay && list.indexOf(day.overnight_stay) === -1) {
                    list.push(day.overnight_stay);
                }
            });
            return list;
        }

        function lookupDestinationLocation(name) {
            var deferred = $.Deferred();
            var terms = buildLocationSearchTerms(name);
            if (!terms.length || !isValidLocationName(terms[0])) {
                deferred.resolve(null);
                return deferred.promise();
            }

            function tryTerm(index) {
                if (index >= terms.length) {
                    deferred.resolve(null);
                    return;
                }
                resolveMaster('destination', terms[index]).done(function (item) {
                    if (item && item.id) {
                        deferred.resolve(item);
                    } else {
                        tryTerm(index + 1);
                    }
                }).fail(function () {
                    tryTerm(index + 1);
                });
            }

            tryTerm(0);
            return deferred.promise();
        }

        function lookupHotel(cityId, hotelName) {
            return resolveMaster('hotel', cleanSimilarSuffix(hotelName), {
                city_id: cityId || ''
            });
        }

        function setCitySelectValue($select, city) {
            if (!$select.length || !city || city.id === null || city.id === undefined || city.id === '') {
                return;
            }
            var idStr = String(city.id);
            if (!$select.find('option').filter(function () { return this.value === idStr; }).length) {
                $select.append(new Option(city.text || idStr, idStr, true, true));
            }
            $select.val(idStr);
            if ($select.data('select2')) {
                $select.trigger('change.select2');
            } else {
                $select.trigger('change');
            }
        }

        function getDestinationGroupMeta(group) {
            var g = String(group || 'city').toLowerCase();
            if (g === 'hptel') {
                g = 'hotel';
            }
            return {
                value: g,
                label: g.charAt(0).toUpperCase() + g.slice(1) + ' Name'
            };
        }

        function setDestinationSelectValue($select, item) {
            if (!$select.length || !item || !item.id) {
                return;
            }
            var idStr = String(item.id);
            var groupMeta = getDestinationGroupMeta(item.group || idStr.split('-')[0]);
            var $group = $select.find('optgroup[value="' + groupMeta.value + '"]');
            if (!$group.length) {
                $group = $('<optgroup></optgroup>').attr('value', groupMeta.value).attr('label', groupMeta.label);
                $select.append($group);
            }
            if (!$group.find('option').filter(function () { return this.value === idStr; }).length) {
                $group.append(new Option(item.text || idStr, idStr, true, true));
            }
            $select.val(idStr);
            if ($select.data('select2')) {
                $select.trigger('change.select2');
            } else {
                $select.trigger('change');
            }
        }

        function matchVehicleInSelect($select, vehicleName) {
            var search = cleanSimilarSuffix(vehicleName).toLowerCase();
            if (!$select.length || !search) {
                return '';
            }
            var matched = '';
            var bestScore = 0;
            $select.find('option').each(function () {
                var val = $(this).val();
                var text = $(this).text();
                if (!val) {
                    return;
                }
                var score = fuzzyMatchScore(search, text);
                if (score > bestScore) {
                    bestScore = score;
                    matched = val;
                }
            });
            return bestScore >= 30 ? matched : '';
        }

        function calcNightsFromDates(checkIn, checkOut) {
            if (!checkIn || !checkOut) {
                return '';
            }
            var inDate = new Date(checkIn);
            var outDate = new Date(checkOut);
            if (isNaN(inDate.getTime()) || isNaN(outDate.getTime())) {
                return '';
            }
            var diff = Math.round((outDate - inDate) / (1000 * 60 * 60 * 24));
            return diff > 0 ? String(diff) : '';
        }

        function matchDestinationSelect(destName) {
            if (!destName) {
                return;
            }
            var bestVal = '';
            var bestScore = 0;
            $('#dest_name_s option').each(function () {
                var val = $(this).val();
                var text = $(this).text();
                if (!val) {
                    return;
                }
                var score = fuzzyMatchScore(destName, text);
                if (score > bestScore) {
                    bestScore = score;
                    bestVal = val;
                }
            });
            if (bestVal && bestScore >= 50) {
                // Dest change would wipe AI-filled sections; skip reset and set code only.
                window.packageSkipDestReset = true;
                $('#dest_name_s').val(bestVal).trigger('change');
                window.packageSkipDestReset = false;
            }
        }

        function ensureTableRows(tableId, neededCount, afterAddRow) {
            var deferred = $.Deferred();
            var table = document.getElementById(tableId);
            if (!table) {
                deferred.resolve();
                return deferred.promise();
            }

            function step() {
                if (table.rows.length >= neededCount) {
                    deferred.resolve();
                    return;
                }
                addRow(tableId);
                if (typeof afterAddRow === 'function') {
                    afterAddRow(table.rows[table.rows.length - 1], table.rows.length);
                }
                setTimeout(step, 120);
            }

            step();
            return deferred.promise();
        }

        function ensureItineraryTable(dayCount) {
            var nights = Math.max(parseInt(dayCount, 10) - 1, 0);
            $('#total_nights').val(nights);
            $('#total_days').val(dayCount);
            return $.post('generate_program_list.php', { total_days: dayCount });
        }

        function fillItineraryRows(programs) {
            var table = document.getElementById('dynamic_table_list');
            if (!table || !Array.isArray(programs) || !programs.length) {
                return false;
            }
            programs.forEach(function (item, index) {
                var row = table.rows[index];
                if (!row) {
                    return;
                }
                var $row = $(row);
                $row.find('input[name="special_attaraction"], input[id^="special_attaraction"]').val(item && item.special_attraction ? item.special_attraction : '');
                $row.find('textarea[name="day_program"], textarea[id^="day_program"]').val(item && item.day_wise_program ? item.day_wise_program : '');
                $row.find('input[name="overnight_stay"], input[id^="overnight_stay"]').val(item && item.overnight_stay ? item.overnight_stay : '');
                selectMealPlan($row.find('select[name="meal_plan"], select[id^="meal_plan"]'), item ? item.meal_plan : null);
            });
            return true;
        }

        function applyHotelSelectMatch($hotelSelect, hotelName, hotelData, $hotelType, $totalNights, nights) {
            var bestVal = matchHotelInSelect($hotelSelect, hotelName);
            if (bestVal) {
                $hotelSelect.val(bestVal).trigger('change');
            } else if (hotelData && hotelData.category) {
                $hotelType.val(hotelData.category);
            }
            if (nights) {
                $totalNights.val(nights);
            }
        }

        function reloadHotelSelectOptions($hotelSelect, optionsHtml) {
            if ($hotelSelect.data('select2')) {
                $hotelSelect.select2('destroy');
            }
            $hotelSelect.html(optionsHtml);
            $hotelSelect.select2({
                width: '100%',
                minimumResultsForSearch: 0,
                dropdownParent: $('body')
            });
            captureHotelSelect2Config($hotelSelect);
            initHotelSelectAddNew($hotelSelect);
        }

        function setHotelSelectValue($hotelSelect, hotel, hotelData, $hotelType, $totalNights, nights) {
            if (hotel && hotel.id) {
                if (!$hotelSelect.find('option').filter(function () { return this.value === String(hotel.id); }).length) {
                    $hotelSelect.append(new Option(hotel.text || hotel.id, hotel.id, true, true));
                }
                $hotelSelect.val(String(hotel.id));
                if ($hotelSelect.data('select2')) {
                    $hotelSelect.trigger('change.select2');
                } else {
                    $hotelSelect.trigger('change');
                }
                if (hotel.category) {
                    $hotelType.val(hotel.category);
                } else if (hotelData && hotelData.category) {
                    $hotelType.val(hotelData.category);
                }
            } else {
                applyHotelSelectMatch($hotelSelect, hotelData.hotel_name, hotelData, $hotelType, $totalNights, nights);
            }
            if (nights) {
                $totalNights.val(nights);
            }
        }

        function fillHotelRow(row, hotelData, parsed) {
            var deferred = $.Deferred();
            var els = getHotelRowElements(row);
            var cityName = hotelData && hotelData.city_name ? hotelData.city_name : '';
            var hotelName = hotelData && hotelData.hotel_name ? hotelData.hotel_name : '';
            var nights = calcNightsFromDates(hotelData.check_in_date, hotelData.check_out_date);
            var cityFallbacks = getHotelCityFallbacks(parsed);

            if (hotelData && hotelData.category) {
                els.$hotelType.val(hotelData.category);
            }
            if (nights) {
                els.$totalNights.val(nights);
            }

            lookupCity(cityName, cityFallbacks).done(function (city) {
                var cityId = '';
                if (city) {
                    setCitySelectValue(els.$citySelect, city);
                    cityId = els.$citySelect.val();
                }

                if (!hotelName) {
                    deferred.resolve();
                    return;
                }

                lookupHotel(cityId, hotelName).done(function (hotel) {
                    if (cityId) {
                        $.get(packageHotelLoadUrl, { city_id: cityId }).done(function (optionsHtml) {
                            reloadHotelSelectOptions(els.$hotelSelect, optionsHtml);
                            setHotelSelectValue(els.$hotelSelect, hotel, hotelData, els.$hotelType, els.$totalNights, nights);
                            deferred.resolve();
                        }).fail(function () {
                            setHotelSelectValue(els.$hotelSelect, hotel, hotelData, els.$hotelType, els.$totalNights, nights);
                            deferred.resolve();
                        });
                    } else {
                        setHotelSelectValue(els.$hotelSelect, hotel, hotelData, els.$hotelType, els.$totalNights, nights);
                        deferred.resolve();
                    }
                }).fail(function () {
                    applyHotelSelectMatch(els.$hotelSelect, hotelName, hotelData, els.$hotelType, els.$totalNights, nights);
                    deferred.resolve();
                });
            });

            return deferred.promise();
        }

        function fillHotelsFromParsed(hotels, parsed) {
            hotels = (hotels || []).filter(function (hotel) {
                return hotel && (hotel.city_name || hotel.hotel_name);
            });
            if (!hotels.length) {
                return $.Deferred().resolve().promise();
            }

            // Always start from one clean row so AI cannot append onto leftover hotels.
            resetHotelTableToSingleRow();

            return ensureTableRows('tbl_package_hotel_master', hotels.length, function (row, rowIndex) {
                if (rowIndex <= 1) {
                    return;
                }
                var $row = $(row);
                var $citySelect = $row.find('select[name^="city_name"]');
                city_lzloading($citySelect);
                $citySelect.attr('onchange', 'hotel_name_list_load(this.id);');
                var $hotelSelect = $row.find('select[name^="hotel_name"]');
                if ($hotelSelect.length && !$hotelSelect.data('select2')) {
                    $hotelSelect.select2({
                        width: '100%',
                        minimumResultsForSearch: 0,
                        dropdownParent: $('body')
                    });
                    captureHotelSelect2Config($hotelSelect);
                    initHotelSelectAddNew($hotelSelect);
                }
            }).then(function () {
                var table = document.getElementById('tbl_package_hotel_master');
                var chain = $.Deferred().resolve().promise();
                hotels.forEach(function (hotel, index) {
                    chain = chain.then(function () {
                        return fillHotelRow(table.rows[index], hotel, parsed);
                    });
                });
                return chain;
            });
        }

        function initTransportRowSelects(row) {
            var els = getTransportRowElements(row);
            destinationLoading(els.$pickupSelect, 'Pickup Location');
            destinationLoading(els.$dropSelect, 'Drop-off Location');
            if (els.$vehicleSelect.length && !els.$vehicleSelect.data('select2')) {
                els.$vehicleSelect.select2({ width: '100%' });
            }
            if (typeof initAllVehicleSelectAddNew === 'function') {
                initAllVehicleSelectAddNew($(row));
            }
        }

        function fillTransportRow(row, vehicleData) {
            var deferred = $.Deferred();
            var els = getTransportRowElements(row);
            var vehicleId = matchVehicleInSelect(els.$vehicleSelect, vehicleData.vehicle_name);

            if (vehicleId) {
                els.$vehicleSelect.val(vehicleId).trigger('change');
            }

            $.when(
                lookupDestinationLocation(vehicleData.pickup_from),
                lookupDestinationLocation(vehicleData.drop_to)
            ).done(function (pickup, drop) {
                if (pickup && pickup.id) {
                    setDestinationSelectValue(els.$pickupSelect, pickup);
                }
                if (drop && drop.id) {
                    setDestinationSelectValue(els.$dropSelect, drop);
                }
                deferred.resolve();
            }).fail(function () {
                deferred.resolve();
            });

            return deferred.promise();
        }

        function fillTransportFromParsed(vehicles) {
            vehicles = (vehicles || []).filter(function (vehicle) {
                if (!vehicle) {
                    return false;
                }
                return vehicle.vehicle_name || isValidLocationName(vehicle.pickup_from) || isValidLocationName(vehicle.drop_to);
            });
            if (!vehicles.length) {
                return $.Deferred().resolve().promise();
            }

            // Always start from one clean row so AI cannot append onto leftover transport.
            resetTransportTableToSingleRow();
            var table = document.getElementById('tbl_package_tour_transport');

            return ensureTableRows('tbl_package_tour_transport', vehicles.length, function (row, rowIndex) {
                if (rowIndex > 1) {
                    initTransportRowSelects(row);
                }
            }).then(function () {
                var chain = $.Deferred().resolve().promise();
                vehicles.forEach(function (vehicle, index) {
                    chain = chain.then(function () {
                        return fillTransportRow(table.rows[index], vehicle);
                    });
                });
                return chain;
            });
        }

        function applyParsedData(parsed) {
            var itinerary = parsed && parsed.itinerary ? parsed.itinerary : {};
            var programs = itinerary.detailed_program || [];
            var destinations = normalizeAiList(itinerary.destination);
            var dayCount = programs.length || parseInt(itinerary.total_days, 10) || 0;
            var generationAtStart = (window.packageFormGeneration || 0) + 1;
            window.packageFormGeneration = generationAtStart;

            // Wipe any previous destinations hotels / transport before AI fill.
            resetHotelTableToSingleRow();
            resetTransportTableToSingleRow();

            if (destinations.length) {
                matchDestinationSelect(destinations[0]);
            } else if (parsed.hotels && parsed.hotels.length && parsed.hotels[0].city_name) {
                matchDestinationSelect(parsed.hotels[0].city_name);
            }

            setWysiwygContent('inclusions', itinerary.inclusions);
            setWysiwygContent('exclusions', itinerary.exclusions);

            var chain = $.Deferred().resolve().promise();

            if (dayCount > 0) {
                chain = chain.then(function () {
                    if (window.packageFormGeneration !== generationAtStart) {
                        return $.Deferred().reject('stale-ai-generation').promise();
                    }
                    return ensureItineraryTable(dayCount).then(function (html) {
                        if (window.packageFormGeneration !== generationAtStart) {
                            return;
                        }
                        $('#div_list1').html(html);
                        fillItineraryRows(programs);
                    });
                });
            }

            return chain
                .then(function () {
                    if (window.packageFormGeneration !== generationAtStart) {
                        return $.Deferred().reject('stale-ai-generation').promise();
                    }
                    return fillHotelsFromParsed(parsed.hotels || [], parsed);
                })
                .then(function () {
                    if (window.packageFormGeneration !== generationAtStart) {
                        return $.Deferred().reject('stale-ai-generation').promise();
                    }
                    return fillTransportFromParsed(parsed.vehicle || []);
                })
                .fail(function (reason) {
                    if (reason === 'stale-ai-generation') {
                        console.log('PACKAGE CREATE: Discarded stale AI fill after destination change');
                    }
                });
        }

        $('#btnAnalyseMessage').on('click', function () {
            var message = stripPasteContent($('#aiMessageInput').val());
            var base_url = $('#base_url').val();

            if (!message) {
                $('#aiApiInfo').text('Please paste quotation or itinerary text.');
                return;
            }

             $('#aiApiInfo').html('<i class="fa fa-spinner fa-spin"></i> <span>Please Wait...</span>');
            $('#btnAnalyseMessage').prop('disabled', true);

            $.ajax({
                type: 'post',
                url: base_url + 'controller/gemini/gemini.php',
                dataType: 'json',
                data: { text: message },
                success: function (response) {
                    if (response && response.error) {
                        showAiStatus(response.error);
                        return;
                    }

                    if (!(response && response.status)) {
                        var errorMsg = (response && (response.error || response.Error))
                            ? (response.error || response.Error)
                            : 'Failed to analyse message.';
                        showAiStatus(errorMsg);
                        return;
                    }

                    var parsed = null;
                    try {
                        parsed = JSON.parse(response.reply || '{}');
                    } catch (e) {
                        parsed = null;
                    }
                    if (!parsed && response.raw) {
                        parsed = response.raw;
                    }
                    if (!parsed || parsed.Error) {
                        showAiStatus((parsed && parsed.Error) ? parsed.Error : 'Invalid AI response.');
                        return;
                    }

                    showAiStatus('Filling package details...');
                    applyParsedData(parsed).done(function () {
                        var programs = (parsed.itinerary && parsed.itinerary.detailed_program) ? parsed.itinerary.detailed_program : [];
                        var hotelCount = (parsed.hotels || []).filter(function (hotel) {
                            return hotel && (hotel.city_name || hotel.hotel_name);
                        }).length;
                        var statusMsg = programs.length
                            ? 'Package details filled successfully.'
                            : 'AI analysis completed.';
                        if (hotelCount) {
                            statusMsg += ' Hotels populated.';
                        }
                        showAiStatus(statusMsg);
                    });
                },
                error: function (xhr) {
                    var errorMsg = 'Request failed.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    } else if (xhr && xhr.responseText) {
                        errorMsg = xhr.responseText;
                    }
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
    })(jQuery);

</script>