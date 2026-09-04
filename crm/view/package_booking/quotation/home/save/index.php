<?php
include "../../../../../model/model.php";
include_once('../../../../layouts/fullwidth_app_header.php');

global $package_flight_switch, $package_cruise_switch, $package_train_switch, $room_category_switch;
$hide_flight = ($package_flight_switch == 'Yes') ? 'hidden' : '';
$hide_cruise = ($package_cruise_switch == 'Yes') ? 'hidden' : '';
$hide_train = ($package_train_switch == 'Yes') ? 'hidden' : '';

$login_id = $_SESSION['login_id'];
$role = $_SESSION['role'];
$emp_id = $_SESSION['emp_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$q = "select * from branch_assign where link='package_booking/quotation/home/index.php'";
$sq_count = mysqli_num_rows(mysqlQuery($q));
$sq = mysqli_fetch_assoc(mysqlQuery($q));
$branch_status = ($sq_count > 0 && $sq['branch_status'] !== NULL && isset($sq['branch_status'])) ? $sq['branch_status'] : 'no';
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
                <span class="text">Daywise Images</span>
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

<div class="bk_tabs bg-white" >
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

<input type="hidden" id="base_url" value="<?= BASE_URL ?>" />

<script>
    // Fresh create form — drop stale Tab4 currency/costing from a previous draft
    try {
        sessionStorage.removeItem('quotation_tab4_costing_state');
        sessionStorage.removeItem('quotation_tab4_travel_cost_state');
        sessionStorage.removeItem('quotation_tab4_costing_visited');
    } catch (e) {}

    $('#enquiry_id, #currency_code').select2();
    // Keep create-form currency on company profile default (not a leftover session value)
    if (typeof quotationInitCostingCurrencyToCompanyDefault === 'function') {
        quotationInitCostingCurrencyToCompanyDefault();
    } else {
        var companyCur = $('#currency_code').attr('data-company-currency')
            || $('#currency_code_pp').attr('data-company-currency')
            || '';
        if (companyCur) {
            $('#currency_code, #currency_code_pp').val(companyCur).trigger('change.select2');
        }
    }

    $('#from_date, #to_date, #quotation_date').datetimepicker({
        timepicker: false,
        format: 'd-m-Y',
        minDate: 0  // Disable past dates - 0 means today, -1 would allow yesterday
    });
    $('#txt_arrval1,#txt_dapart1,#train_arrival_date,#train_departure_date').datetimepicker({
        format: 'd-m-Y H:i',
        parentID: 'body',
        fixed: true,
        scrollInput: false
    });
    /**Hotel Name load start**/
    if (typeof hotelSupplierQuickLoadUrl === 'undefined') {
        hotelSupplierQuickLoadUrl = $('#base_url').val() + 'view/package_booking/quotation/home/hotel/hotel_name_load.php';
    }
    // Hotel name list load is defined in footer_scripts.js (resolveHotelSelectFromCity).

    function quotationSelectDefaultRoomCategory(count, onComplete) {
        var $roomCat = $("#room_cat-" + count);
        if (!$roomCat.length) {
            if (typeof onComplete === 'function') {
                onComplete();
            }
            return;
        }
        var deluxeOption = $roomCat.find("option[value*='Deluxe']");
        if (deluxeOption.length > 0) {
            $roomCat.val(deluxeOption.first().val());
        } else {
            var firstOption = $roomCat.find('option').filter(function () {
                return this.value && this.value !== '';
            }).first();
            if (firstOption.length) {
                $roomCat.val(firstOption.val());
            }
        }
        $roomCat.trigger('change.select2');
        if (typeof onComplete === 'function') {
            onComplete();
        }
    }

    function hotel_type_load_cate(id, onComplete) {
        var hotel_id = $("#" + id).val();
        var count = typeof parseQuotationHotelRowSuffix === 'function'
            ? parseQuotationHotelRowSuffix(id)
            : id.substring(11);
        if (!hotel_id || !count) {
            if (typeof onComplete === 'function') {
                onComplete();
            }
            return;
        }
        var base_url = $('#base_url').val();
        $.get(base_url + "view/package_booking/quotation/home/hotel/hotel_category.php", {
            hotel_id: hotel_id
        }, function(data) {
            $("#room_cat-" + count).html(data);
            if (typeof refreshRoomCategorySelectAfterLoad === 'function') {
                refreshRoomCategorySelectAfterLoad("#room_cat-" + count, { width: '145px' });
            }
            setTimeout(function () {
                quotationSelectDefaultRoomCategory(count, onComplete);
            }, 100);
        }).fail(function () {
            if (typeof onComplete === 'function') {
                onComplete();
            }
        });
    }

    function hotel_type_load(id, onComplete) {
        var hotel_id = $("#" + id).val();
        var count = typeof parseQuotationHotelRowSuffix === 'function'
            ? parseQuotationHotelRowSuffix(id)
            : id.substring(10);
        if (!hotel_id) {
            if (typeof onComplete === 'function') {
                onComplete();
            }
            return;
        }
        var base_url = $('#base_url').val();
        $.get(base_url + "view/package_booking/quotation/home/hotel/hotel_type_load.php", {
            hotel_id: hotel_id
        }, function(data) {
            $("#hotel_type-" + count).val(data);
        });
        hotel_type_load_cate(id, onComplete);
    }

    function hotel_type_load1(id) {
        var hotel_id = $("#" + id).val();
        var count = id.substring(10);
        $.get("../hotel/hotel_type_load.php", {
            hotel_id: hotel_id
        }, function(data) {
            $("#hotel_type" + count).val(data);
        });

    }
    function setQuotationCitySelect(cityEl, cityId, cityName) {
        if (!cityEl || !cityId) {
            return;
        }
        var $city = $(cityEl);
        if ($city.data('select2')) {
            $city.select2('destroy');
        }
        city_lzloading(cityEl);
        var savedOnchange = cityEl.getAttribute('onchange');
        if (savedOnchange) {
            cityEl.removeAttribute('onchange');
        }
        if (typeof selectCityInLazyDropdown === 'function') {
            selectCityInLazyDropdown($city, cityId, cityName, { triggerChange: false });
        } else {
            $city.append(new Option(cityName, cityId, true, true));
            $city.val(String(cityId)).trigger('change.select2');
        }
        if (savedOnchange) {
            cityEl.setAttribute('onchange', savedOnchange);
        }
    }

    function applyQuotationHotelSelect($hotelSelect, hotelId, hotelName, onComplete) {
        if (!$hotelSelect || !$hotelSelect.length || !hotelId) {
            if (typeof onComplete === 'function') {
                onComplete();
            }
            return;
        }
        var id = String(hotelId);
        var hotelEl = $hotelSelect[0];
        var savedOnchange = hotelEl.getAttribute('onchange');
        if (savedOnchange) {
            hotelEl.removeAttribute('onchange');
        }
        if ($hotelSelect.find('option').filter(function () { return String(this.value) === id; }).length === 0) {
            $hotelSelect.append($('<option></option>').attr('value', id).text(hotelName || ''));
        }
        $hotelSelect.val(id).trigger('change.select2');
        if (typeof initHotelSelectAddNew === 'function') {
            initHotelSelectAddNew($hotelSelect);
        }
        if (savedOnchange) {
            hotelEl.setAttribute('onchange', savedOnchange);
        }
        var selectId = $hotelSelect.attr('id') || '';
        if (selectId && typeof hotel_type_load === 'function') {
            hotel_type_load(selectId, onComplete);
        } else if (typeof onComplete === 'function') {
            onComplete();
        }
    }

    function loadQuotationHotelFromPackage(hotelData, $hotelSelect, onComplete) {
        if (!hotelData || !$hotelSelect || !$hotelSelect.length) {
            if (typeof onComplete === 'function') {
                onComplete();
            }
            return;
        }
        var finish = function () {
            if (typeof onComplete === 'function') {
                onComplete();
            }
        };
        if (hotelData.city_id && typeof hotelDropdownLoadByCity === 'function') {
            hotelDropdownLoadByCity(hotelData.city_id, $hotelSelect, function () {
                if (hotelData.hotel_id1) {
                    applyQuotationHotelSelect($hotelSelect, hotelData.hotel_id1, hotelData.hotel_name, finish);
                } else {
                    finish();
                }
            });
        } else if (hotelData.hotel_id1) {
            $hotelSelect.html('<option value="' + hotelData.hotel_id1 + '">' + (hotelData.hotel_name || '') + '</option>');
            if (!$hotelSelect.data('select2')) {
                $hotelSelect.select2({ width: '160px', minimumResultsForSearch: 0 });
            }
            applyQuotationHotelSelect($hotelSelect, hotelData.hotel_id1, hotelData.hotel_name, finish);
        } else {
            finish();
        }
    }

    function populateQuotationHotelRowsSequential(table, hotel_arr, options) {
        options = options || {};
        var dataIndex = 0;
        var baseRowIndex = typeof options.startRowIndex === 'number' ? options.startRowIndex : 0;

        function loadRow() {
            if (dataIndex >= hotel_arr.length) {
                if (typeof options.onComplete === 'function') {
                    options.onComplete();
                }
                return;
            }

            var row = table.rows[baseRowIndex + dataIndex];
            var hotel = hotel_arr[dataIndex];
            if (!row || !hotel) {
                dataIndex++;
                loadRow();
                return;
            }

            var rowOptions = $.extend({}, options);
            if (options.templateReferenceRows && options.templateReferenceRows.length) {
                rowOptions.referenceRow = options.templateReferenceRows[dataIndex]
                    || options.templateReferenceRows[options.templateReferenceRows.length - 1];
            } else if (!options.freshPackageLoad && dataIndex > 0) {
                var prevRow = table.rows[baseRowIndex + dataIndex - 1];
                if (prevRow && typeof quotationGetHotelRowReference === 'function') {
                    var prevRef = quotationGetHotelRowReference(prevRow);
                    if (prevRef) {
                        rowOptions.referenceRow = prevRef;
                    }
                }
            } else if (options.referenceRow) {
                rowOptions.referenceRow = options.referenceRow;
            }

            if (typeof populateHotelRow === 'function') {
                populateHotelRow(row, hotel, dataIndex, hotel_arr, rowOptions, function () {
                    dataIndex++;
                    loadRow();
                });
            } else {
                dataIndex++;
                loadRow();
            }
        }

        loadRow();
    }
    /**Excursion Name load**/
    function get_excursion_list(id) {
        var city_id = $("#" + id).val();
        var base_url = $('#base_url').val();
        var count = id.replace(/^city_name-/, '');
        var $excursion = $("#excursion-" + count);

        $.post(base_url + "view/package_booking/quotation/home/excursion_name_load.php", {
            city_id: city_id
        }, function(data) {
            if ($excursion.data('select2')) {
                $excursion.select2('destroy');
            }
            $excursion.empty().html(data);
            $excursion.select2({ width: '150px' });
        });
    }

    /**Excursion Amount load**/
    function get_excursion_amount() {
        var base_url = $('#base_url').val();
        var exc_date_arr = new Array();
        var exc_arr = new Array();
        var transfer_arr = new Array();
        var adult_arr = new Array();
        var child_arr = new Array();
        var childwo_arr = new Array();
        var infant_arr = new Array();
        var total_vehicles_arr = [];
        var vehicle_arr = [];

        var table = document.getElementById("tbl_package_tour_quotation_dynamic_excursion");
        var rowCount = table.rows.length;

        for (var i = 0; i < rowCount; i++) {

            var row = table.rows[i];
            var exc_date = row.cells[2].childNodes[0].value;
            var exc = $(row.cells[4].childNodes[0]).val() || '';
            var transfer = $(row.cells[5].childNodes[0]).val() || '';

            var total_adult = row.cells[6].childNodes[0].value;
            var total_children = row.cells[7].childNodes[0].value;
            var total_childrenwo = row.cells[8].childNodes[0].value;
            var total_infant = row.cells[9].childNodes[0].value;
            var vehicle_id = row.cells[15] ? ($(row.cells[15].childNodes[0]).val() || row.cells[15].childNodes[0].value || '') : '';
            var total_vehicles = row.cells[16] ? row.cells[16].childNodes[0].value : 0;
            if (typeof quotationExcursionVehicleCount === 'function') {
                total_vehicles = quotationExcursionVehicleCount(transfer, total_vehicles);
                if (row.cells[16] && row.cells[16].childNodes[0] && quotationExcursionNeedsTransfer(transfer)
                    && (!row.cells[16].childNodes[0].value || parseInt(row.cells[16].childNodes[0].value, 10) < 1)) {
                    row.cells[16].childNodes[0].value = total_vehicles;
                }
            }

            total_adult = (total_adult == '') ? 0 : total_adult;
            total_children = (total_children == '') ? 0 : total_children;
            total_childrenwo = (total_childrenwo == '') ? 0 : total_childrenwo;
            total_infant = (total_infant == '') ? 0 : total_infant;
            total_vehicles = (total_vehicles == '') ? 0 : total_vehicles;

            exc_date_arr.push(exc_date);
            exc_arr.push(exc);
            transfer_arr.push(transfer);
            adult_arr.push(total_adult);
            child_arr.push(total_children);
            childwo_arr.push(total_childrenwo);
            infant_arr.push(total_infant);
            vehicle_arr.push(vehicle_id || '');
            total_vehicles_arr.push(total_vehicles);
        }
        var exc_adult_cost = 0;
        var exc_child_cot = 0;
        var exc_childwo_cot = 0;
        var exc_infant_cost = 0;
        $.post(base_url + "view/package_booking/quotation/home/excursion_amount_load.php", {
            exc_date_arr: exc_date_arr,
            exc_arr: exc_arr,
            transfer_arr: transfer_arr,
            adult_arr: adult_arr,
            child_arr: child_arr,
            childwo_arr: childwo_arr,
            infant_arr: infant_arr,
            vehicle_arr: vehicle_arr,
            total_vehicles_arr: total_vehicles_arr
        }, function(data) {
            var amount_arr = JSON.parse(data);
            for (var i = 0; i < amount_arr.length; i++) {

                var row = table.rows[i];
                if (row.cells[0].childNodes[0].checked) {

                    row.cells[10].childNodes[0].value = amount_arr[i]['total_cost'];
                    row.cells[11].childNodes[0].value = amount_arr[i]['adult_cost'];
                    row.cells[12].childNodes[0].value = amount_arr[i]['child_cost'];
                    row.cells[13].childNodes[0].value = amount_arr[i]['childwo_cost'];
                    row.cells[14].childNodes[0].value = amount_arr[i]['infant_cost'];
                    if (typeof quotationSetExcursionRowTransferCost === 'function') {
                        quotationSetExcursionRowTransferCost(row, amount_arr[i]['transfer_cost'] || 0);
                    } else {
                        row.setAttribute('data-transfer-cost', amount_arr[i]['transfer_cost'] || 0);
                    }
                } else {
                    row.cells[10].childNodes[0].value = 0;
                    row.cells[11].childNodes[0].value = 0;
                    row.cells[12].childNodes[0].value = 0;
                    row.cells[13].childNodes[0].value = 0;
                    row.cells[14].childNodes[0].value = 0;
                    if (typeof quotationSetExcursionRowTransferCost === 'function') {
                        quotationSetExcursionRowTransferCost(row, 0);
                    } else {
                        row.setAttribute('data-transfer-cost', 0);
                    }
                }
            }
            if (typeof quotationRefreshCostingAfterActivityTariff === 'function') {
                quotationRefreshCostingAfterActivityTariff({ force: true });
            }
        });
    }
</script>
<script src="<?php echo BASE_URL ?>view/package_booking/quotation/js/quotation.js?v=<?php echo @filemtime(__DIR__ . '/../../js/quotation.js') ?: time(); ?>"></script>
<script src="<?php echo BASE_URL ?>view/package_booking/quotation/js/calculation.js?v=<?php echo @filemtime(__DIR__ . '/../../js/calculation.js') ?: time(); ?>"></script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>
<script src="<?php echo BASE_URL ?>js/app/field_validation.js"></script>
<?php
include_once('../../../../layouts/fullwidth_app_footer.php');
?>